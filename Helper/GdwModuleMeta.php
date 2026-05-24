<?php
declare(strict_types=1);

namespace GDW\DisableImageCache\Helper;

final class GdwModuleMeta
{
    /** @return array{desc:string, config_path:string, config_anchor:string, repo_url:string, docs_url:string} */
    public static function getMeta(): array
    {
        return [
            'desc' => 'Permite desactivar el uso de cache de imágenes para servir la URL original cuando se requiera.',
            'config_path' => 'adminhtml/system_config/edit/section/gdwcatalog',
            'config_anchor' => '#gdwcatalog_disableimagecache-link',
            'repo_url' => 'https://github.com/josecruzchavez/GDW_DisableImageCache',
            'docs_url' => 'https://docs.gdw.mx/modulos/gdw_disableimagecache',
        ];
    }
}
