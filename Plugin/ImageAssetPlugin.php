<?php
namespace GDW\DisableImageCache\Plugin;

use Magento\Catalog\Model\View\Asset\Image as ImageAsset;
use Magento\Framework\UrlInterface;
use GDW\DisableImageCache\Helper\Data as ImageCacheHelper;

class ImageAssetPlugin
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var ImageCacheHelper
     */
    protected $helper;

    public function __construct(
        UrlInterface $urlBuilder,
        ImageCacheHelper $helper
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->helper = $helper;
    }

    /**
     * Devuelve la URL de la imagen ORIGINAL (sin /cache/) solo si el módulo está activo.
     *
     * @param ImageAsset $subject
     * @param string     $result URL generada por Magento (con cache)
     * @return string
     */
    public function afterGetUrl(ImageAsset $subject, $result)
    {
        // 🔴 Validación desde el helper / system.xml
        if (!$this->helper->isCacheDisabled()) {
            // Si está desactivado en config, usamos la URL original (con cache)
            return $result;
        }

        try {
            $sourceFile = $subject->getSourceFile();
        } catch (\Exception $e) {
            return $result;
        }

        if (!$sourceFile) {
            return $result;
        }

        $sourceFile = str_replace('\\', '/', $sourceFile);

        $needle = 'catalog/product';
        $pos    = strpos($sourceFile, $needle);

        if ($pos === false) {
            return $result;
        }

        $relativePath = substr($sourceFile, $pos);   // catalog/product/...
        $relativePath = ltrim($relativePath, '/');

        $baseMediaUrl = $this->urlBuilder->getBaseUrl([
            '_type' => UrlInterface::URL_TYPE_MEDIA,
        ]);

        $originalUrl = rtrim($baseMediaUrl, '/') . '/' . $relativePath;

        return $originalUrl;
    }
}