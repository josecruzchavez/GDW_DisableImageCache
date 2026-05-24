<?php
namespace GDW\DisableImageCache\Helper;

use GDW\Core\Helper\Data as CoreHelperData;
use GDW\Core\Util\Parser;

class Data extends CoreHelperData
{
    const XML_PATH_DISABLE_CACHE = 'gdw/catalog_disableimagecache/disable_image_cache';

    /**
     * ¿Está desactivado el cache de imágenes?
     * Si devuelve true, forzamos URL directa de la imagen original.
     */
    public function isCacheDisabled(): bool
    {
        return Parser::bool($this->getConfigValue(self::XML_PATH_DISABLE_CACHE), false);
    }
}
