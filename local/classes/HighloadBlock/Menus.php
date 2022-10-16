<?
namespace Godra\Api\HighloadBlock;

use Godra\Api\Iblock,
    \Bitrix\Iblock\SectionTable;

class Menus extends Base
{
    protected $row_data = [
        'code' => 'UF_CLASS нужного',
    ];

    public function get()
    {
        // получаю элементы
        $res = $this->data_class::getList([
            'select' => ['*'],
            'filter' => [ 'UF_CLASS' => $this->post_data['code'] ]
        ])->fetchAll();

        // перевожу названия
        foreach ($res as &$item)
            foreach ($item as $row_name => $row)
            {
                $item[str_replace('UF_', '', $row_name)] = $row;
                unset($item[$row_name]);
            }

        // получаю структуру инфоблока
        foreach ($res as &$item)
            if((bool) $item['IS_IBLOCK'])
                $item['items'] = $this->getIblockMenu($item['URL']);

        return $res;
    }

    protected function getIblockMenu($api_code)
    {
        $iblock_id = \Bitrix\Iblock\IblockTable::getList([
            'filter' => ['API_CODE' => $api_code],
            'limit'  => 1,
            'select' => ['ID']
        ])->fetch()['ID'];

        $three = $this->getSectionsTree($iblock_id);
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/local/file_put.php',print_r($three,1));
        return $three;
    }

    protected function getSectionsTree($iblock_id)
    {
        $filter = [
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => $iblock_id,
            'GLOBAL_ACTIVE'=>'Y',
        ];
        $order  = [ 'DEPTH_LEVEL' => 'ASC','SORT' => 'ASC'];
        $select = ['IBLOCK_ID', 'ID','NAME', 'CODE','IBLOCK_SECTION_ID', 'SECTION_PAGE_URL_RAW' => 'IBLOCK.SECTION_PAGE_URL'];

        $rs_sections = SectionTable::GetList([
            'order'  => $order,
            'select' => $select,
            'filter' => $filter,
        ])->fetchAll();

        $section_link = [];
        $result = [];
        $section_link[0] = &$result;

        foreach($rs_sections as $section)
        {
            $section_link[ intval($section['IBLOCK_SECTION_ID']) ]['items'][$section['ID']] = [
                'name' => $section['NAME'],
                'url' => \CIBlock::ReplaceDetailUrl($section['SECTION_PAGE_URL_RAW'], $section, true, 'S')
            ];
            $section_link[$section['ID']] = &$section_link[intval($section['IBLOCK_SECTION_ID'])]['items'][$section['ID']];
        }

        unset($section_link);

        return $result['items'];
    }
}
?>