<?
namespace Godra\Api\Catalog;

class Element extends Base
{
    /**
     * Отдаётся при /api/map
     * * Реализованные поля:
     * * * element_code Для выборки одного элемента
     * * * section_code Для выборки всех элементов раздела
     * @var array
     */
    protected static $row_data = [
        'order' => [
            'mandatory' => false,
            'description' => 'Сортировка вида { name: название поля, direction: направлениесортировки(asc/desc) }'
        ],
        'section_code' => [
            'mandatory' => false,
            'alias' => 'CODE',
            'description' => 'Символьный код раздела'
        ],
        'element_code' => [
            'mandatory' => false,
            'alias' => 'CODE',
            'description' => 'Символьный код товара'
        ],
        'limit' => [
            'mandatory' => false,
            'alias' => 'limit',
            'description' => 'Кол-во товаров'
        ],
        'page' => [
            'mandatory' => false,
            'alias' => 'page',
            'description' => 'Текущая страница  '
        ],
    ];

    /**
     * Апи код информационного блока каталога
     * @var string
     */
    protected static $api_ib_code = IBLOCK_CATALOG_API;

    /**
     * Поля для выборки из ElementTable
     * * Принимает параметры:
     * * * name => название поля,
     * * * method => метод работы с резултатом ( например \\CFile::getPath или \\CFile::getPath($var) )
     * @var array
     */
    protected static $select_rows = [
        [ 'name' => 'NAME'],
        [ 'name' => 'CODE'],
        [ 'name' => 'DETAIL_TEXT'],
        [ 'name' => 'PREVIEW_TEXT'],
        [ 'name' => 'SHOW_COUNTER'],
        [ 'name' => 'ID' , 'alias' => 'id'],
        [ 'name' => 'PREVIEW_PICTURE', 'method' => '\\CFile::getPath'],
        [ 'name' => 'DETAIL_PICTURE',  'method' => '\\CFile::getPath'],
    ];

    /**
     * Поля выборки из ProductTable
     * @var array
     */
    protected static $select_product_rows = [
        [ 'name' => 'ID' , 'alias' => 'id'],
        [ 'name' => 'NAME'],
    ];

    /**
     * Получить список товаров, или 1 товар при наличии code в post_data
     * @return void|array
     */
    public static function getList()
    {
        $products = self::get();

        foreach ($products as &$product)
        {
            $product['price']  = self::getPrice($product['id'], $product['props']['Базовая единица'][0]['description']);
            $product['amount'] = self::getAmountById($product['id']);
        }

        return $products;
    }

    public static function getViewed()
    {
        $result = [];
        $products_array = self::getViewedProducts();

        foreach ($products_array as $value)
        {
            $result = array_merge($result, self::get($value));
        }

        return $result;
    }

    /**
     * Получить количество элементов по фильтру, работает как self::getList
     * @return int
     */
    public static function getCount()
    {
        return self::countElementsBySectionCode();
    }
}
?>