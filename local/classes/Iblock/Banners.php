<?
namespace Godra\Api\Iblock;

class Banners extends Base
{
    protected static $row_data = [
        'code' => [
            'mandatory' => false,
            'alias' => 'CODE',
            'description' => 'Символьный код баннера'
        ]
    ];

    protected static $select_rows = [
        [ 'name' => 'ID' ],
        [ 'name' => 'all_width_picture', 'method' => '\\CFile::GetPath'],
        [ 'name' => 'button_content', 'alias' => 'button_url'],
        [ 'name' => 'NAME'],
        [ 'name' => 'PREVIEW_TEXT'],
        [ 'name' => 'SORT'],
        [ 'name' => 'CODE', 'alias' => 'element_code'],
    ];

    protected static $api_ib_code = IBLOCK_BANNERS_API;

    public static function getList()
    {
        return self::get();
    }
}
?>