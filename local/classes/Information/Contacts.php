<?
namespace Godra\Api\Information;

use Bitrix\Main\Config\Option;

class Contacts
{
    /**
     * Поля из настроек которые следует стянуть
     *
     * @var array
     */
    protected static $rows = [
        'phone',
        'address',
        'work_time',
        'email',
        'short_name',
        'full_name',
        'origin_address',
        'ur_address',
        'inn',
        'kpp',
        'ogrn',
        'okpo',
    ];

    /**
     * Наполняет массив значениями из админки (настройки апи)
     *
     * @return void
     */
    public static function getList()
    {
        foreach (self::$rows as $val)
            $result[$val] = Option::get('main', 'api_'.$val);

        return $result;
    }
}
?>