<?
namespace Godra\Api\Iblock;

class Stock extends Base
{
    protected static $row_data = [
    ];

    protected static $select_rows = [
        [ 'name' => 'ID' ],
        [ 'name' => 'PREVIEW_PICTURE', 'method' => '\\CFile::GetPath'],
        [ 'name' => 'DETAIL_PICTURE', 'method' => '\\CFile::GetPath'],
        [ 'name' => 'NAME'],
        [ 'name' => 'DETAIL_TEXT'],
        [ 'name' => 'PREVIEW_TEXT'],
        [ 'name' => 'FILES', 'method' => '\\CFile::GetPath'],
    ];

    protected static $api_ib_code = IBLOCK_STOCK_API;

    public static function getList()
    {
        return self::get();
    }
}
?>