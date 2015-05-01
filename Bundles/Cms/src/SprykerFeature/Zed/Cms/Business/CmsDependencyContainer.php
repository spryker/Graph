<?php

namespace SprykerFeature\Zed\Cms\Business;

use Generated\Zed\Ide\AutoCompletion;
use Generated\Zed\Ide\FactoryAutoCompletion\CmsBusiness;
use SprykerEngine\Shared\Kernel\LocatorLocatorInterface;
use SprykerEngine\Zed\Kernel\Business\AbstractDependencyContainer;
use SprykerFeature\Zed\Cms\Business\Mapping\GlossaryKeyMappingManagerInterface;
use SprykerFeature\Zed\Cms\Business\Page\PageManagerInterface;
use SprykerFeature\Zed\Cms\Business\Template\TemplateManagerInterface;
use SprykerFeature\Zed\Cms\Dependency\Facade\CmsToGlossaryInterface;
use SprykerFeature\Zed\Cms\Dependency\Facade\CmsToTouchInterface;
use sprykerfeature\Zed\Cms\Dependency\Facade\CmsToUrlInterface;
use SprykerFeature\Zed\Cms\Persistence\CmsQueryContainerInterface;

/**
 * @method CmsBusiness getFactory()
 */
class CmsDependencyContainer extends AbstractDependencyContainer
{
    /**
     * @return CmsQueryContainerInterface
     */
    protected function getCmsQueryContainer()
    {
        return $this->getLocator()->cms()->queryContainer();
    }

    /**
     * @return PageManagerInterface
     */
    public function getPageManager()
    {
        return $this->getFactory()->createPagePageManager(
            $this->getCmsQueryContainer(),
            $this->getTemplateManager(),
            $this->getGlossaryFacade(),
            $this->getTouchFacade(),
            $this->getUrlFacade(),
            $this->getLocator()
        );
    }

    /**
     * @return TemplateManagerInterface
     */
    public function getTemplateManager()
    {
        return $this->getFactory()->createTemplateTemplateManager(
            $this->getCmsQueryContainer(),
            $this->getLocator()
        );
    }

    /**
     * @return GlossaryKeyMappingManagerInterface
     */
    public function getGlossaryKeyMappingManager()
    {
        return $this->getFactory()->createMappingGlossaryKeyMappingManager(
            $this->getGlossaryFacade(),
            $this->getCmsQueryContainer(),
            $this->getTemplateManager(),
            $this->getLocator()
        );
    }

    /**
     * @return CmsToGlossaryInterface
     */
    protected function getGlossaryFacade()
    {
        return $this->getLocator()->glossary()->facade();
    }

    /**
     * @return CmsToTouchInterface
     */
    protected function getTouchFacade()
    {
        return $this->getLocator()->touch()->facade();
    }

    /**
     * @return CmsToUrlInterface
     */
    protected function getUrlFacade()
    {
        return $this->getLocator()->url()->facade();
    }
}