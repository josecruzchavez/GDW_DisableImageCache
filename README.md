![gdw_disableimagecache](https://medios.gdw.mx/github_assets/gdw_disableimagecache/gdw_disableimagecache_01.jpg)

# GDW_DisableImageCache

GDW_DisableImageCache es un módulo para Magento 2 que permite controlar de forma explícita cuándo usar o no las rutas de caché de imágenes.

Cuando la opción está habilitada, el módulo evita que Magento entregue URL con segmento `/cache/` y devuelve la ruta original del archivo en `media/catalog/product`.
Esto es útil en escenarios donde necesitas que la imagen publicada corresponda exactamente al archivo fuente y no a una versión derivada por resize/cache.

El módulo está orientado principalmente a:

- integraciones externas que consumen URL directas de imagen,
- validaciones de QA donde se requiere comparar archivo original vs. salida en frontend,
- depuración de problemas visuales provocados por regeneración de caché o variaciones de tamaño.

Importante: al desactivar la ruta de caché, puedes afectar tiempos de carga o comportamiento visual en algunos contextos, por lo que se recomienda activar esta opción de forma controlada y validar impacto por tienda.

## Compatibilidad

- Rama 4.4.x: Magento Open Source / Adobe Commerce 2.4.4+ con PHP 8.1+ (serie recomendada `^4.4`)
- Rama 4.x: Magento Open Source / Adobe Commerce 2.4.0 a 2.4.3 con PHP 7.4 (serie recomendada `^4.0`)
- Rama 3.x: Magento Open Source / Adobe Commerce 2.3.x con PHP 7.4 (serie recomendada `^3.0`)

## Dependencias

- `gdw/core` `^4.4`
- `magento/framework` `>=103.0.4 <104.0.0`
- `php` `>=8.1`

## Instalación

Ejecutar en la raíz de Magento.


```bash
composer require gdw/disableimagecache
php bin/magento module:enable GDW_DisableImageCache
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
php bin/magento cache:flush
```

## Actualización

```bash
composer update gdw/disableimagecache
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
php bin/magento cache:flush
```

## Eliminación

```bash
php bin/magento module:disable GDW_DisableImageCache
composer remove gdw/disableimagecache
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
php bin/magento cache:flush
```

### Importante

Si usas una rama distinta a 4.4.x, verifica antes que esa rama exista en el repositorio y que su versión de gdw/core sea compatible.

## Configuración

Ruta en admin:

`Stores > Configuration > GDW | Gestión Digital Web > Catalog > Disable Image Cache`

Campo principal:

- `Desactivar caché de imágenes`: cuando está en `Sí`, el módulo intenta devolver URL sin segmento `/cache/` para imágenes de catálogo y assets relacionados.

## Notas técnicas

- El módulo depende de `GDW_Core` y extiende su helper base para lectura de configuración.
- Incluye plugins para:
  - `Magento\Catalog\Helper\Image`
  - `Magento\Catalog\Model\View\Asset\Image`
  - `Magento\Framework\View\Asset\Repository`

## Repositorio

- GitHub: https://github.com/josecruzchavez/GDW_DisableImageCache

## Documentación

- https://docs.gdw.mx/modulos/gdw_disableimagecache

## Changelog

Consulta el changelog del módulo en:

- https://docs.gdw.mx/modulos/gdw_disableimagecache/changelog
