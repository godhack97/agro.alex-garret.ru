<?
namespace Godra\Api\Iblock;

class Dashboard extends Base
{
    protected static $row_data = [
        'code'
    ];

    protected static $select_rows = [
        [ 'name' => 'SORT' ],
        [ 'name' => 'PREVIEW_PICTURE', 'method' => '\\CFile::GetPath'],
        [ 'name' => 'PREVIEW_TEXT'],
        [ 'name' => 'NAME'],
    ];

    protected static $api_ib_code = IBLOCK_DASHBOARD_API;

    public static function getList()
    {
        return self::get();
    }
}
?>