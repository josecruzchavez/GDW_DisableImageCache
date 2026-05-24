<?php
namespace GDW\DisableImageCache\Plugin;

use Magento\Framework\View\Asset\Repository;
use GDW\DisableImageCache\Helper\Data as ConfigHelper;

/**
 * Plugin genérico sobre el repositorio de assets.
 *
 * Cuando el caché de imágenes está desactivado, este plugin intenta
 * "limpiar" cualquier URL que contenga /media/.../cache/ para apuntar
 * a la imagen original sin pasar por la ruta de caché.
 *
 * Esto ayuda a cubrir:
 *  - Imágenes de productos generadas por otros helpers
 *  - Imágenes de categorías
 *  - Imágenes en CMS que hayan pasado por algún resize/cache
 */
class AssetRepositoryPlugin
{
    /**
     * @var ConfigHelper
     */
    protected $configHelper;

    public function __construct(
        ConfigHelper $configHelper
    ) {
        $this->configHelper = $configHelper;
    }

    /**
     * afterGetUrlWithParams
     *
     * @param Repository $subject
     * @param string     $result URL final del asset
     * @param string     $fileId
     * @param array<string, mixed> $params
     * @return string
     */
    public function afterGetUrlWithParams(Repository $subject, $result, $fileId, array $params = [])
    {
        if (!$this->configHelper->isCacheDisabled()) {
            return $result;
        }

        if (!is_string($result) || $result === '') {
            return $result;
        }

        // Solo tocamos URLs que van a /media/
        if (strpos($result, '/media/') === false) {
            return $result;
        }

        // Parseamos la URL para trabajar solo con la ruta
        $parts = parse_url($result);
        if (!isset($parts['path'])) {
            return $result;
        }

        $path = $parts['path'];

        // Si la ruta no contiene /cache/, no hacemos nada
        $cachePos = strpos($path, '/cache/');
        if ($cachePos === false) {
            return $result;
        }

        // Ejemplo de path:
        // /media/catalog/product/cache/abcd1234/small_image/imagen.jpg
        // Queremos convertirlo en:
        // /media/catalog/product/imagen.jpg

        $beforeCache = substr($path, 0, $cachePos); // /media/catalog/product
        $afterCache  = substr($path, $cachePos + strlen('/cache/')); // abcd1234/small_image/imagen.jpg

        // Saltamos el primer segmento (hash o tamaño) y nos quedamos con lo que sigue
        $firstSlashPos = strpos($afterCache, '/');
        if ($firstSlashPos !== false) {
            $afterCache = substr($afterCache, $firstSlashPos + 1); // small_image/imagen.jpg
        } else {
            // No tiene más segmentos, algo raro, regresamos sin cambiar
            return $result;
        }

        // Opcional: saltar un segundo nivel (por si hay tamaño/variant)
        $secondSlashPos = strpos($afterCache, '/');
        if ($secondSlashPos !== false) {
            $afterCache = substr($afterCache, $secondSlashPos + 1); // imagen.jpg
        }

        // Construimos la nueva ruta sin /cache/
        $newPath = rtrim($beforeCache, '/') . '/' . ltrim($afterCache, '/');

        // Reconstruimos la URL completa
        $newUrl = $newPath;
        if (isset($parts['scheme']) && isset($parts['host'])) {
            $newUrl = $parts['scheme'] . '://' . $parts['host']
                . (isset($parts['port']) ? ':' . $parts['port'] : '')
                . $newPath;
        }

        if (isset($parts['query'])) {
            $newUrl .= '?' . $parts['query'];
        }
        if (isset($parts['fragment'])) {
            $newUrl .= '#' . $parts['fragment'];
        }

        return $newUrl;
    }
}
