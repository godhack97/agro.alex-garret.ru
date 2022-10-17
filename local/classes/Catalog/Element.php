<?
namespace Godra\Api\Catalog;

class Element extends Base
{
    protected static $row_data = [];
    protected static $api_ib_code = IBLOCK_CATALOG_API;

    protected static $select_rows = [
        [ 'name' => 'ID' , 'alias' => 'id'],
        [ 'name' => 'NAME'],
        [ 'name' => 'PREVIEW_PICTURE'],
        [ 'name' => 'DETAIL_PICTURE'],

    ];

    protected static $select_product_rows = [
        [ 'name' => 'ID' , 'alias' => 'id'],
        [ 'name' => 'NAME'],
        [ 'name' => 'PREVIEW_PICTURE'],
        [ 'name' => 'DETAIL_PICTURE'],

    ];

    public static function getList()
    {
        return self::get();
    }
}
?>