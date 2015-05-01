<?php
namespace SprykerFeature\Shared\Library\Storage;

abstract class StorageKeyGenerator
{
    const KEY_SEPERATOR = '.';

    /**
     * @param string $key
     * @return string
     * @static
     */
    protected static function escapeKey($key)
    {
        $charsToReplace = array('"', "'", ' ', "\0", "\n", "\r");
        return str_replace($charsToReplace, '-', mb_strtolower(trim($key)));
    }

    /**
     * @param string $key
     * @return string
     * @static
     */
    protected static function prependStoreName($key)
    {
        $storeName = \SprykerEngine\Shared\Kernel\Store::getInstance()->getStoreName();
        return $storeName . self::KEY_SEPERATOR . $key;
    }
}