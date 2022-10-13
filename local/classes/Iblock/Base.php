<?
namespace Godra\Api\Iblock;

use Bitrix\Iblock\Elements,
    Bitrix\Iblock\IblockTable,
    Bitrix\Iblock\ElementTable,
    Bitrix\Iblock\SectionTable,
    Godra\Api\Helpers\Utility\Misc;

abstract class Base
{
    /**
     * Обязательные поля
     *
     * @var array
     */
    protected static $row_data = [
        'code'
    ];

    /**
     * поля для выборки
     * name: имя поля, если нужно алиас => имя
     * method: метод для обработки поля псле получения
     *
     * @var array
     */
    protected static $select_rows = [];


    /**
     * Код Апи Информационного блока
     *
     * @var string|boolean
     */
    protected static $api_ib_code = false;

    /**
     * Абстрактный метод реализации получения данных
     */
    abstract public static function getList();

    public static function getMap()
    {
        return static::$row_data;
    }

    /**
     * Получить баннера по коду раздела
     *
     * @param $data['code'] $section_id_or_code
     * @return void
     */
    // Тут надо сделать условие, что если кода нет - возвращаем все эллементы
    public static function get()
    {
        Misc::includeModules(['iblock']);

        // получение данных из post
        $data = Misc::getPostDataFromJson();

        // Проверка входящих данных, отдаст ошибки 3й аргумент
        Misc::checkRows($data, static::$row_data, $result['errors']);

        // получение элементов
        $entity = static::getEntityName(static::$api_ib_code);
        $select = array_column(static::$select_rows, 'name');
        $section_id  = static::getSectionByFilter(['CODE' => $data['code']]);

        $collection = $entity::getList([
            'select' => $select,
            'filter' => ['IBLOCK_SECTION_ID' => $section_id]
        ])->fetchCollection();

        foreach ($collection as $item)
        {
            foreach($select as $key => $name)
            {
                $name = \strtolower($name);
                $field  = $item->get($name);
                $method = static::$select_rows[$key]['method'];

                $new_item[$name] = \is_object($field) ? $field->getValue() : $field;
                $new_item[$name] = $method ? $method($new_item[$name]) : $new_item[$name];
            }

            $result['items'][] = $new_item;
        }

        return count($result['errors']) ? $result['errors'] : $result['items'];
    }


    /**
     * Простой конструктор entity name по Апи коду инфоблока
     * @param string $iblock_api_code Апи код инфоблока
     * @return string
     */
    protected static function getEntityName($iblock_api_code)
    {
        return '\\Bitrix\\Iblock\\Elements\\Element'.ucfirst($iblock_api_code).'Table';
    }


    /**
     * Получить баннера по id раздела
     *
     * @param int|string $ids id раздела
     * @return array|void
     */
    protected function GetBySectionIdOrCode($id_or_code)
    {
        Misc::includeModules(['iblock']);

        $id = \is_int($id_or_code) ?
            $id_or_code :
            static::getSectionByFilter(['CODE' => $id_or_code]);


        if($id)
            $db_res = ElementTable::getList([
                'filter' => ['IBLOCK_SECTION_ID' => $id, 'ACTIVE' => 'Y'],
                'select' => ['ID'],
            ])->fetchAll();

        if(!count($db_res))
            $result['errors'][] = 'Ничего не найдено';
        else
            $result['data'] = \array_column($db_res, 'ID');

        return $result['errors'] ?: $result['data'];
    }


    /**
     * Получить id раздела по фильтру D7
     *
     * @param array $filter
     * @return void
     */
    protected static function getSectionByFilter($filter)
    {
        Misc::includeModules(['iblock']);

        return SectionTable::getList([
            'filter' => $filter,
            'select' => ['ID']
        ])->fetch()['ID'] ?: false;
    }
}
?>