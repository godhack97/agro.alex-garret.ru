<?
namespace Godra\Api\Iblock;

use Bitrix\Iblock\ElementTable,
    Bitrix\Iblock\SectionTable,
    Godra\Api\Helpers\Utility\Misc;

abstract class Base
{
    protected static $row_data = [];
    protected static $select_rows = [];
    protected static $api_ib_code = false;

    /**
     * Поля товара для выборки
     * name: имя поля, если нужно алиас => имя
     * method: метод для обработки поля псле получения
     * @var array
     */
    protected static $select_product_rows = [];

    /**
     * Получить товары по коду раздела
     * @param $data['code'] $section_id_or_code
     * @return void
     */
    // Тут надо сделать условие, что если кода нет - возвращаем все эллементы
    public static function get()
    {
        Misc::includeModules(['iblock', 'catalog']);

        // получение данных из post
        $data = Misc::getPostDataFromJson();

        // Проверка входящих данных, отдаст ошибки 3й аргумент
        Misc::checkRows($data, static::$row_data, $result['errors']);

        // получение сущности
        $entity = static::getEntityName(static::$api_ib_code);
        $entity = $entity::getEntity();

        $select = array_column(static::$select_rows, 'name');

        // Реализация новых полей
        self::addDetailPageUrlForEntity($entity);
        self::addSectionNameForEntity($entity);
        self::addSectionCodeForEntity($entity);

        // Добавление доп полей в выборку
        $select[] = 'URL';
        $select[] = 'SECTION_NAME';
        $select[] = 'SECTION_CODE';

        $section_id  = static::getSectionByFilter(['CODE' => $data['section_code']]);

        // код раздела
        $filter = $section_id ?
            ['IBLOCK_SECTION_ID' => $section_id] : [];

        // код элемента
        $data['element_code'] ?
            $filter['CODE']  = $data['element_code'] : false;

        // выборка
        $query = new \Bitrix\Main\Entity\Query($entity);

        $collection = $query
        ->setSelect($select)
        ->setFilter($filter)
        ->exec()
        ->fetchCollection();

        // обработка значений
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

    /**
     * Добавляет сущности новое поля URL
     * @param [type] $entity
     * @return void
     */
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

    /**
     * Исполнят метод переданный в виде строки с меткой для данных $val
     * Пример {
     *      $method = "str_replace('world', '', $val)";
     *      $value = 'Hello world'
     *      вернёт 'Hello';
     * }
     * @param string $method
     * @param [type] $value
     */
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
     * Получить id раздела по фильтру D7
     * @param array $filter
     * @return array|void
     */
    protected static function getSectionByFilter($filter)
    {
        Misc::includeModules(['iblock']);

        return SectionTable::getList([
            'filter' => $filter,
            'select' => ['ID'],
            'limit'  => []
        ])->fetch()['ID'] ?: false;
    }

    /**
     * Получить инфоблок по символьному коду
     * @param string $code
     * @return array|void
     */
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