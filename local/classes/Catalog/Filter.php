<?
namespace Godra\Api\Catalog;

use Godra\Api\Iblock\IblockElementPropertyTable;

class Filter extends Base
{
    function __construct()
    {
        parent::__construct();

        $this->getAllProps();
    }

    protected function getAllProps()
    {
        $props = IblockElementPropertyTable::GetList([
            'filter' => ['IBLOCK_PROPERTY_ID' => 36],
            'select' => ['VALUE', 'IBLOCK_PROPERTY_ID', 'DESCRIPTION']
        ])->fetchAll();

        echo '<pre>';
        print_r($props);
        echo '</pre>';

    }
}