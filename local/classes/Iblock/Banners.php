<?
namespace Godra\Api\Iblock;

class Banners extends Base
{
    protected static $row_data = [
        'code'
    ];

    protected static $select_rows = [
        [ 'name' => 'ID' ],
        [ 'name' => 'all_width_picture', 'method' => '\\CFile::GetPath'],
        [ 'name' => 'button_content'],
        [ 'name' => 'NAME'],
    ];

    protected static $api_ib_code = IBLOCK_BANNERS_API;

    public static function getList()
    {
        return self::get();
    }
}
?>