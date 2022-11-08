<?
namespace Godra\Api\Iblock;

class Stock extends Base
{
    protected static $row_data = [
    ];

    protected static $select_rows = [
        [ 'name' => 'ID' ],
        [ 'name' => 'SORT'],
        [ 'name' => 'NAME'],
        [ 'name' => 'DETAIL_TEXT'],
        [ 'name' => 'PREVIEW_TEXT'],
        [ 'name' => 'CODE', 'alias' => 'element_code'],
        [ 'name' => 'PREVIEW_PICTURE', 'method' => '\\CFile::GetPath'],
        [ 'name' => 'DETAIL_PICTURE', 'method' => '\\CFile::GetPath'],
    ];

    protected static $api_ib_code = IBLOCK_STOCK_API;

    public static function getList()
    {
        return self::get();
    }
}
?>