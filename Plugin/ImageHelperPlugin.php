<?php
namespace GDW\DisableImageCache\Plugin;

use Magento\Catalog\Helper\Image as ImageHelper;
use GDW\DisableImageCache\Helper\Data as ConfigHelper;

/**
 * Plugin sobre el helper de imágenes de producto.
 *
 * Cuando la opción "Desactivar caché de imágenes" está en "Sí",
 * este plugin limpia la URL generada por el helper y quita la
 * parte de /cache/ para apuntar a la imagen original.
 */
class ImageHelperPlugin
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
     * Después de obtener la URL de la imagen, quitar /cache/ si está desactivado.
     *
     * @param ImageHelper $subject
     * @param string      $result URL original (con o sin caché)
     * @return string
     */
    public function afterGetUrl(ImageHelper $subject, $result)
    {
        // Si NO queremos desactivar el caché, respetamos el comportamiento normal
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

        // Si la ruta no contiene /cache/, no hacemos nada
        if (strpos($result, '/cache/') === false) {
            return $result;
        }

        // Parseamos la URL para trabajar solo con la ruta
        $parts = parse_url($result);
        if (!isset($parts['path'])) {
            return $result;
        }

        $path = $parts['path'];

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

        // Saltamos el primer segmento (hash o tamaño)
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