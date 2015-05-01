<?php

/*
 * (c) Copyright Spryker Systems GmbH 2015
 */

namespace SprykerFeature\Zed\Glossary\Business\Translation;

use Generated\Zed\Ide\AutoCompletion;
use SprykerEngine\Shared\Dto\LocaleDto;
use SprykerEngine\Shared\Kernel\LocatorLocatorInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Propel;
use SprykerEngine\Zed\Locale\Business\Exception\MissingLocaleException;
use SprykerFeature\Shared\Glossary\Transfer\Translation;
use SprykerFeature\Zed\Glossary\Business\Exception\MissingKeyException;
use SprykerFeature\Zed\Glossary\Business\Exception\MissingTranslationException;
use SprykerFeature\Zed\Glossary\Business\Exception\TranslationExistsException;
use SprykerFeature\Zed\Glossary\Business\Key\KeyManagerInterface;
use SprykerFeature\Zed\Glossary\Dependency\Facade\GlossaryToLocaleInterface;
use SprykerFeature\Zed\Glossary\Dependency\Facade\GlossaryToTouchInterface;
use SprykerFeature\Zed\Glossary\Persistence\GlossaryQueryContainerInterface;
use SprykerFeature\Zed\Glossary\Persistence\Propel\SpyGlossaryTranslation;
use SprykerFeature\Zed\Glossary\Persistence\Propel\Map\SpyGlossaryTranslationTableMap;

class TranslationManager implements TranslationManagerInterface
{
    const TOUCH_TRANSLATION = 'translation';
    /**
     * @var GlossaryQueryContainerInterface
     */
    protected $glossaryQueryContainer;

    /**
     * @var GlossaryToTouchInterface
     */
    protected $touchFacade;

    /**
     * @var KeyManagerInterface
     */
    protected $keyManager;

    /**
     * @var GlossaryToLocaleInterface
     */
    protected $localeFacade;

    /**
     * @var AutoCompletion
     */
    protected $locator;

    /**
     * @param GlossaryQueryContainerInterface $glossaryQueryContainer
     * @param GlossaryToTouchInterface $touchFacade
     * @param GlossaryToLocaleInterface $localeFacade
     * @param KeyManagerInterface $keyManager
     * @param LocatorLocatorInterface $locator
     */
    public function __construct(
        GlossaryQueryContainerInterface $glossaryQueryContainer,
        GlossaryToTouchInterface $touchFacade,
        GlossaryToLocaleInterface $localeFacade,
        KeyManagerInterface $keyManager,
        LocatorLocatorInterface $locator
    ) {
        $this->glossaryQueryContainer = $glossaryQueryContainer;
        $this->touchFacade = $touchFacade;
        $this->keyManager = $keyManager;
        $this->localeFacade = $localeFacade;
        $this->locator = $locator;
    }

    /**
     * @param string $keyName
     * @param LocaleDto $locale
     * @param string $value
     * @param bool $isActive
     *
     * @return Translation
     * @throws MissingKeyException
     * @throws MissingLocaleException
     * @throws TranslationExistsException
     */
    public function createTranslation($keyName, LocaleDto $locale, $value, $isActive)
    {
        $idKey = $this->keyManager->getKey($keyName)->getPrimaryKey();
        $idLocale = $locale->getIdLocale();

        if (null === $idLocale) {
            $idLocale = $this->localeFacade->getLocale($locale->getLocaleName())->getIdLocale();
        }

        return $this->createTranslationByIds($idKey, $idLocale, $value, $isActive);
    }

    /**
     * @param int $idKey
     * @param int $idLocale
     *
     * @throws TranslationExistsException
     */
    protected function checkTranslationDoesNotExist($idKey, $idLocale)
    {
        if ($this->hasTranslationByIds($idKey, $idLocale)) {
            throw new TranslationExistsException(
                sprintf(
                    'Tried to create a translation for keyId %s, localeId %s, but it already exists',
                    $idKey,
                    $idLocale
                )
            );
        };
    }

    /**
     * @param int $idKey
     * @param int $idLocale
     *
     * @return bool
     */
    protected function hasTranslationByIds($idKey, $idLocale)
    {
        $translationCount = $this->glossaryQueryContainer
            ->queryTranslationByIds($idKey, $idLocale)
            ->count()
        ;

        return $translationCount > 0;
    }

    /**
     * @param string $keyName
     * @param LocaleDto $locale
     *
     * @return bool
     */
    public function hasTranslation($keyName, LocaleDto $locale)
    {
        $translationCount = $this->glossaryQueryContainer
            ->queryTranslationByNames($keyName, $locale->getLocaleName())
            ->count()
        ;

        return $translationCount > 0;
    }

    /**
     * @param int $idKey
     * @param int $idLocale
     * @param string $value
     * @param bool $isActive
     *
     * @return Translation
     * @throws \Exception
     * @throws PropelException
     */
    protected function createTranslationByIds($idKey, $idLocale, $value, $isActive)
    {
        $this->checkTranslationDoesNotExist($idKey, $idLocale);

        $translation = $this->locator->glossary()->entitySpyGlossaryTranslation();

        $translation
            ->setFkGlossaryKey($idKey)
            ->setFkLocale($idLocale)
            ->setValue($value)
            ->setIsActive($isActive)
        ;

        $translation->save();

        return $this->convertEntityToTranslationTransfer($translation);
    }

    /**
     * @param int $idItem
     */
    protected function insertActiveTouchRecord($idItem)
    {
        $this->touchFacade->touchActive(
            self::TOUCH_TRANSLATION,
            $idItem
        );
    }

    /**
     * @param SpyGlossaryTranslation $translation
     *
     * @return Translation
     */
    protected function convertEntityToTranslationTransfer(SpyGlossaryTranslation $translation)
    {
        $transferTranslation = new \Generated\Shared\Transfer\GlossaryTranslationTransfer();
        $transferTranslation->fromArray($translation->toArray());

        return $transferTranslation;
    }

    /**
     * @param string $keyName
     * @param LocaleDto $locale
     * @param string $value
     * @param bool $isActive
     *
     * @return Translation
     * @throws MissingTranslationException
     * @throws PropelException
     */
    public function updateTranslation($keyName, LocaleDto $locale, $value, $isActive)
    {
        $translation = $this->getUpdatedTranslationEntity($keyName, $locale, $value, $isActive);

        return $this->doUpdateTranslation($translation);
    }

    /**
     * @param string $keyName
     * @param LocaleDto $locale
     * @param string $value
     * @param bool $isActive
     *
     * @return SpyGlossaryTranslation
     * @throws MissingTranslationException
     */
    protected function getUpdatedTranslationEntity($keyName, $locale, $value, $isActive)
    {
        $translation = $this->getTranslationEntityByNames($keyName, $locale->getLocaleName());

        $translation->setValue($value);
        $translation->setIsActive($isActive);

        return $translation;
    }

    /**
     * @param string $keyName
     * @param LocaleDto $locale
     *
     * @return Translation
     * @throws MissingTranslationException
     */
    public function getTranslationByKeyName($keyName, LocaleDto $locale)
    {
        $translation = $this->getTranslationEntityByNames($keyName, $locale->getLocaleName());

        return $this->convertEntityToTranslationTransfer($translation);
    }

    /**
     * @param int $idItem
     */
    protected function insertDeletedTouchRecord($idItem)
    {
        $this->touchFacade->touchDeleted(
            self::TOUCH_TRANSLATION,
            $idItem
        );
    }

    /**
     * @param string $keyName
     * @param LocaleDto $locale
     *
     * @return bool
     */
    public function deleteTranslation($keyName, LocaleDto $locale)
    {
        if (!$this->hasTranslation($keyName, $locale)) {
            return true;
        }

        $translation = $this->getTranslationEntityByNames($keyName, $locale->getLocaleName());

        $translation->setIsActive(false);

        if ($translation->isModified()) {
            $translation->save();
            $this->insertDeletedTouchRecord($translation->getPrimaryKey());
        }

        return true;
    }

    /**
     * @param string $keyName
     * @param array $data
     *
     * @return string
     * @throws MissingTranslationException
     */
    public function translate($keyName, array $data = [])
    {
        $locale = $this->localeFacade->getCurrentLocale();
        $translation = $this->getTranslationByKeyName($keyName, $locale);

        return str_replace(array_keys($data), array_values($data), $translation->getValue());
    }

    /**
     * @param Translation $transferTranslation
     *
     * @return Translation
     * @throws MissingKeyException
     * @throws MissingLocaleException
     * @throws TranslationExistsException
     * @throws MissingTranslationException
     */
    public function saveTranslation(Translation $transferTranslation)
    {
        if (is_null($transferTranslation->getIdGlossaryTranslation())) {
            $translationTransfer = $this->createTranslationFromTransfer($transferTranslation);
            $transferTranslation->setIdGlossaryTranslation($translationTransfer->getIdGlossaryTranslation());

            return $transferTranslation;
        } else {
            $translationTransfer = $this->getTranslationFromTransfer($transferTranslation);
            $this->doUpdateTranslation($translationTransfer);

            return $transferTranslation;
        }
    }

    /**
     * @param Translation $transferTranslation
     *
     * @return Translation
     * @throws MissingKeyException
     * @throws MissingLocaleException
     * @throws TranslationExistsException
     * @throws MissingTranslationException
     */
    public function saveAndTouchTranslation(Translation $transferTranslation)
    {
        if (is_null($transferTranslation->getIdGlossaryTranslation())) {
            $translationEntity = $this->createAndTouchTranslationFromTransfer($transferTranslation);
            $transferTranslation->setIdGlossaryTranslation($translationEntity->getIdGlossaryTranslation());

            return $transferTranslation;
        } else {
            $translationEntity = $this->getTranslationFromTransfer($transferTranslation);
            $this->doUpdateAndTouchTranslation($translationEntity);

            return $transferTranslation;
        }
    }

    /**
     * @param Translation $transferTranslation
     *
     * @return Translation
     */
    protected function createTranslationFromTransfer(Translation $transferTranslation)
    {
        $newEntity = $this->createTranslationByIds(
            $transferTranslation->getFkGlossaryKey(),
            $transferTranslation->getFkLocale(),
            $transferTranslation->getValue(),
            $transferTranslation->getIsActive()
        );

        return $newEntity;
    }

    /**
     * @param Translation $transferTranslation
     *
     * @return SpyGlossaryTranslation
     */
    protected function createAndTouchTranslationFromTransfer(Translation $transferTranslation)
    {
        Propel::getConnection()->beginTransaction();

        $transferTranslationNew = $this->createTranslationFromTransfer($transferTranslation);

        if ($transferTranslationNew->getIsActive()) {
            $this->insertActiveTouchRecord($transferTranslationNew->getIdGlossaryTranslation());
        }

        Propel::getConnection()->commit();

        return $transferTranslationNew;
    }

    /**
     * @param int $idKey
     * @param int $idLocale
     *
     * @return SpyGlossaryTranslation
     * @throws MissingTranslationException
     */
    protected function getTranslationByIds($idKey, $idLocale)
    {
        $translation = $this->glossaryQueryContainer
            ->queryTranslationByIds($idKey, $idLocale)
            ->findOne()
        ;

        if (!$translation) {
            throw new MissingTranslationException(
                sprintf('Could not find a translation for keyId %s, localeId %s', $idKey, $idLocale)
            );
        }

        return $translation;
    }

    /**
     * @param Translation $transferTranslation
     *
     * @return SpyGlossaryTranslation
     * @throws MissingTranslationException
     */
    protected function getTranslationFromTransfer(Translation $transferTranslation)
    {
        $translation = $this->getTranslationEntityById($transferTranslation->getIdGlossaryTranslation());
        $translation->fromArray($transferTranslation->toArray());

        return $translation;
    }

    /**
     * @param int $idKey
     * @param array $data
     *
     * @return string
     * @throws MissingTranslationException
     */
    public function translateByKeyId($idKey, array $data = [])
    {
        $idLocale = $this->localeFacade->getCurrentLocale()->getIdLocale();
        $translation = $this->getTranslationByIds($idKey, $idLocale);

        return str_replace(array_keys($data), array_values($data), $translation->getValue());
    }

    /**
     * @param string $keyName
     * @param string $value
     * @param bool $isActive
     *
     * @return SpyGlossaryTranslation
     * @throws MissingKeyException
     * @throws MissingLocaleException
     * @throws TranslationExistsException
     */
    public function createTranslationForCurrentLocale($keyName, $value, $isActive = true)
    {
        $idKey = $this->keyManager->getKey($keyName)->getPrimaryKey();
        $idLocale = $this->localeFacade->getCurrentLocale()->getIdLocale();

        return $this->createTranslationByIds($idKey, $idLocale, $value, $isActive);
    }

    /**
     * @param string $keyName
     * @param LocaleDto $locale
     * @param string $value
     * @param bool $isActive
     *
     * @return SpyGlossaryTranslation
     * @throws MissingKeyException
     * @throws MissingLocaleException
     * @throws TranslationExistsException
     */
    public function createAndTouchTranslation($keyName, LocaleDto $locale, $value, $isActive = true)
    {
        Propel::getConnection()->beginTransaction();

        $translation = $this->createTranslation($keyName, $locale, $value, $isActive);
        if ($isActive) {
            $this->insertActiveTouchRecord($translation->getIdGlossaryTranslation());
        }
        Propel::getConnection()->commit();

        return $translation;
    }

    /**
     * @param string $keyName
     * @param LocaleDto $locale
     * @param string $value
     * @param bool $isActive
     *
     * @return Translation
     * @throws MissingKeyException
     * @throws MissingLocaleException
     * @throws MissingTranslationException
     */
    public function updateAndTouchTranslation($keyName, LocaleDto $locale, $value, $isActive = true)
    {
        $translation = $this->getUpdatedTranslationEntity($keyName, $locale, $value, $isActive);

        return $this->doUpdateAndTouchTranslation($translation);
    }

    /**
     * @param SpyGlossaryTranslation $translation

     * @return Translation
     */
    protected function doUpdateTranslation(SpyGlossaryTranslation $translation)
    {
        if ($translation->isModified()) {
            $translation->save();
        }

        return $this->convertEntityToTranslationTransfer($translation);
    }

    /**
     * @param SpyGlossaryTranslation $translation

     * @return Translation
     * @throws \Exception
     * @throws PropelException
     */
    protected function doUpdateAndTouchTranslation(SpyGlossaryTranslation $translation)
    {
        if (!$translation->isModified()) {
            return $translation;
        }

        Propel::getConnection()->beginTransaction();

        $isActiveModified = $translation->isColumnModified(
            SpyGlossaryTranslationTableMap::COL_IS_ACTIVE
        );

        $translation->save();

        if ($translation->getIsActive()) {
            $this->insertActiveTouchRecord($translation->getIdGlossaryTranslation());
        } elseif ($isActiveModified) {
            $this->insertDeletedTouchRecord($translation->getIdGlossaryTranslation());
        }

        Propel::getConnection()->commit();

        return $this->convertEntityToTranslationTransfer($translation);
    }

    /**
     * @param int $idTranslation
     *
     * @return Translation
     * @throws MissingTranslationException
     */
    protected function getTranslationById($idTranslation)
    {
        $translation = $this->getTranslationEntityById($idTranslation);

        return $this->convertEntityToTranslationTransfer($translation);
    }

    /**
     * @param int $idKey
     */
    public function touchCurrentTranslationForKeyId($idKey)
    {
        $idLocale = $this->localeFacade->getCurrentLocale()->getIdLocale();
        $translation = $this->getTranslationByIds($idKey, $idLocale);
        $this->insertActiveTouchRecord($translation->getIdGlossaryTranslation());
    }

    /**
     * @param string $keyName
     * @param string $localeName
     *
     * @return SpyGlossaryTranslation
     * @throws MissingTranslationException
     */
    protected function getTranslationEntityByNames($keyName, $localeName)
    {
        $translation = $this->glossaryQueryContainer
            ->queryTranslationByNames($keyName, $localeName)
            ->findOne()
        ;
        if (!$translation) {
            throw new MissingTranslationException(
                sprintf('Could not find a translation for key %s, locale %s', $keyName, $localeName)
            );
        }
        return $translation;
    }

    /**
     * @param int $idTranslation
     *
     * @return SpyGlossaryTranslation
     * @throws MissingTranslationException
     */
    protected function getTranslationEntityById($idTranslation)
    {
        $translation = $this->glossaryQueryContainer->queryTranslations()->findPk($idTranslation);
        if (!$translation) {
            throw new MissingTranslationException(
                sprintf('Could not find a translation with id %s', $idTranslation)
            );
        }
        return $translation;
    }
}