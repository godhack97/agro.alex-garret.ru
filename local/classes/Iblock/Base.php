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
    protected static $row_data = [];

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
        $entity = $entity::getEntity();

        $select = array_column(static::$select_rows, 'name');

        $select[] = 'URL';
        $select[] = 'SECTION_NAME';
        $select[] = 'SECTION_CODE';

        $section_id  = static::getSectionByFilter(['CODE' => $data['code']]);
        $filter = $section_id ? ['IBLOCK_SECTION_ID' => $section_id ?: 0] : [];

        self::addDetailPageUrlForEntity($entity);
        self::addSectionNameForEntity($entity);
        self::addSectionCodeForEntity($entity);

        $query = new \Bitrix\Main\Entity\Query($entity);

        $collection = $query
        ->setSelect($select)
        ->setFilter($filter)
        ->exec()
        ->fetchCollection();

        foreach ($collection as $item)
        {
            foreach($select as $key => $name)
            {
                $field  = $item->get($name);
                $name   = static::$select_rows[$key]['alias'] ?:  \strtolower($name);
                $method = static::$select_rows[$key]['method'];

                // перебор, для случая множественного значения
                $new_item[$name] = \is_object($field) ?
                    (
                        method_exists($field, 'getValue') ?
                            $field->getValue():
                            self::getAllValues($field)
                    ):
                    $field;

                /** Обработка переданных методов в поле method , заменяет $val на значение, если нужен просто результат, а не определённое поле результата, то можно передать только метод */
                if($method)
                    $new_item[$name] = self::executeMethod($method, $new_item[$name]);

                $new_item['url'] = \preg_replace('/[\/]+/m', '/', $new_item['url']);

            }

            $result['items'][] = $new_item;
        }

        return count($result['errors']) ? $result['errors'] : $result['items'];
    }

    protected function addDetailPageUrlForEntity(&$entity)
    {
        // на прозапас сделал вложенность
        $entity->addField(
            new \Bitrix\Main\Entity\ExpressionField(
                'URL',
                '
                CONCAT(
                    "/", COALESCE(%s,""), "/",
                    COALESCE(%s,""), "/",
                    COALESCE(%s,""), "/",
                    COALESCE(%s,""), "/",
                    COALESCE(%s,""), "/",
                    COALESCE(%s,""), "/",
                    COALESCE(%s,""), "/"
                )',
                [
                    'IBLOCK.CODE',
                    'IBLOCK_SECTION.PARENT_SECTION.PARENT_SECTION.PARENT_SECTION.PARENT_SECTION.CODE',
                    'IBLOCK_SECTION.PARENT_SECTION.PARENT_SECTION.PARENT_SECTION.CODE',
                    'IBLOCK_SECTION.PARENT_SECTION.PARENT_SECTION.CODE',
                    'IBLOCK_SECTION.PARENT_SECTION.CODE',
                    'IBLOCK_SECTION.CODE',
                    'CODE',
                ]
            )
          );
    }

    protected function addSectionNameForEntity(&$entity)
    {
        $entity->addField(
            new \Bitrix\Main\Entity\ExpressionField(
                'SECTION_NAME',
                '
                CONCAT(
                    COALESCE(%s,"")
                )',
                [
                    'IBLOCK_SECTION.NAME',
                ]
            )
          );
    }

    protected function addSectionCodeForEntity(&$entity)
    {
        $entity->addField(
            new \Bitrix\Main\Entity\ExpressionField(
                'SECTION_CODE',
                '
                CONCAT(
                    COALESCE(%s,"")
                )',
                [
                    'IBLOCK_SECTION.CODE',
                ]
            )
          );
    }

    protected static function getAllValues($field)
    {
        foreach ($field as $val)
            $res[] = $val->getValue();

        return $res;
    }

    protected static function executeMethod($method, $value)
    {
            if(\is_array($value))
            {
                foreach ($value as $val)
                    strpos($method, '$val')?
                        eval('$arr[] = '.\str_replace('$val', $val , $method ).';'):
                        $arr[] = $method($val);
            }
            else
                strpos($method, '$val')?
                    eval('$arr = '.\str_replace('$val', $value , $method ).';'):
                    $arr = $method($value);

        return $arr;
    }

    /**
     * Простой конструктор entity name по Апи коду инфоблока
     * @param string $iblock_api_code Апи код инфоблока
     * @return string
     */
    public static function getEntityName($iblock_api_code)
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

    public static function getIblockByCode($code)
    {
        return \Bitrix\Iblock\IblockTable::getList([
            'filter' => ['CODE' => $code],
            'limit' => 1,
            'select' => ['ID', 'NAME', 'LIST_PAGE_URL']
        ])->fetch();
    }
}
?>