<?
namespace Godra\Api\EventHandlers\Admin;

class Handler
{
    public static function addMenuItem(&$aGlobalMenu, &$aModuleMenu)
    {
        global $USER;

        if ($USER->IsAdmin())
        {
            $aGlobalMenu['global_menu_api'] = [
                'menu_id' => 'api',
                    "icon" => "default_menu_icon",
                    "page_icon" => "default_page_icon",
                'text' => 'Настройки Api',
                'title' => 'Настройки Api',
                'url' => 'settings.php?lang=ru',
                'sort' => 0,
                'items_id' => 'global_menu_api',
                'help_section' => 'api',
                'items' => [
                    [
                        'parent_menu' => 'global_menu_api',
                        'sort'        => 10,
                        'url'         => '/local/admin/settings.php?lang=ru',
                        'text'        => 'Настройки',
                        'title'       => 'Настройки',
                        'icon'        => 'fav_menu_icon',
                        'page_icon'   => 'fav_menu_icon',
                        'items_id'    => 'menu_api',
                    ],
                ],
            ];
        }
    }
}
?>