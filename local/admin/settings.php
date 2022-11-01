<?
use Bitrix\Main\Config\Option;
use \Godra\Api\Helpers\Utility\Misc;

require $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin.php";?>
<?
//  Обработка формы
if($_POST['apply'] OR $_POST['save'])
    foreach ($_POST as $key => $value)
        Option::set('main', 'api_'.$key , trim($value));

// Табы
$aTabs = [
    [
        "DIV" => "general_settings", "TAB" => (($bEdit) ? "Настройки сайта" : "Настройки сайта"),
        "ICON" => "fileman", "TITLE" => (($bEdit) ? "" : "Основные настройки")
    ],
];

$data = [
    'Контакты' => [
        [
            'title' => 'Номер телефона',
            'tag' => 'input',
            'type' => 'text',
            'name' => 'phone',
            'value' => Option::get('main', 'api_phone') ?: '',
        ],
        [
            'title' => 'Адрес',
            'tag' => 'input',
            'type' => 'text',
            'name' => 'address',
            'value' => Option::get('main', 'api_address') ?: '',
        ],
        [
            'title' => 'Время работы',
            'tag' => 'input',
            'type' => 'text',
            'name' => 'work_time',
            'value' => Option::get('main', 'api_work_time') ?: '',
        ],
        [
            'title' => 'Электронная почта',
            'tag' => 'input',
            'type' => 'text',
            'name' => 'email',
            'value' => Option::get('main', 'api_email') ?: '',
        ],
    ],

    'Реквизиты' => [
        [
            'title' => 'Краткое название',
            'tag' => 'input',
            'type' => 'text',
            'name' => 'short_name',
            'value' => Option::get('main', 'api_short_name') ?: '',
        ],
        [
            'title' => 'полное название',
            'tag' => 'input',
            'type' => 'text',
            'name' => 'full_name',
            'value' => Option::get('main', 'api_full_name') ?: '',
        ],
        [
            'title' => 'фактический адрес',
            'tag' => 'input',
            'type' => 'text',
            'name' => 'origin_address',
            'value' => Option::get('main', 'api_origin_address') ?: '',
        ],
        [
            'title' => 'юридический адрес',
            'tag' => 'input',
            'type' => 'text',
            'name' => 'ur_address',
            'value' => Option::get('main', 'api_ur_address') ?: '',
        ],
        [
            'title' => 'ИНН',
            'tag' => 'input',
            'type' => 'text',
            'name' => 'inn',
            'value' => Option::get('main', 'api_inn') ?: '',
        ],
        [
            'title' => 'КПП',
            'tag' => 'input',
            'type' => 'text',
            'name' => 'kpp',
            'value' => Option::get('main', 'api_kpp') ?: '',
        ],
        [
            'title' => 'ОГРН',
            'tag' => 'input',
            'type' => 'text',
            'name' => 'ogrn',
            'value' => Option::get('main', 'api_ogrn') ?: '',
        ],
        [
            'title' => 'ОКПО',
            'tag' => 'input',
            'type' => 'text',
            'name' => 'okpo',
            'value' => Option::get('main', 'api_okpo') ?: '',
        ],
    ],

    'Каталог' => [
        [
            'title' => 'Свойство с единицами измерения',
            'tag' => 'select',
            'type' => '',
            'required' => true,
            'name' => 'measures_property_code',
            'value' => Option::get('main', 'api_measures_property_code') ?: '',
            'multiselect_values' => Misc::getCatalogProperties(),
        ]
    ]
];
?>

<form enctype="multipart/form-data" method="POST" action="" name="settings">
    <?
    $tabControl = new CAdminTabControl("tabControl", $aTabs);
    $tabControl->Begin();
    $tabControl->BeginNextTab();

    foreach ($data as $key => $inputs)
    {
        hr($key);
        "<tr>".inputs($inputs)."</tr>";
    }

    $tabControl->BeginNextTab();
    $tabControl->EndTab();
    $tabControl->Buttons([
        "disabled" => $only_read,
        "back_url" => (strlen($back_url)>0 && strpos($back_url, "/bitrix/admin/fileman_file_edit.php")!==0) ? htmlspecialcharsbx($back_url) : "/bitrix/admin/fileman_admin.php?".$addUrl."&site=".Urlencode($site)."&path=".UrlEncode($arParsedPath["PREV"])
    ]);
    $tabControl->End();
    ?>
</form>

<?
// Разделитель настроек
function hr($name)
{
    echo  '<tr class="heading"><td colspan="2"><b>'.$name.'</b></td></tr>';
}

// Название настройки
function input_hr($name)
{
    return  '<td width="10%" class="adm-detail-content-cell-l">'.$name.'</td>';
}

function input($data)
{
    if($data['tag'] == 'input')
    {
        return
        '<tr>
            '.input_hr($data['title']).'
            <td width="40%" class="adm-detail-content-cell-r">
                <'.$data['tag'].' type="'.$data['type'].'" size="40" name="'.$data['name'].'" value="'.$data['value'].'">
            </td>
        </tr>';
    }

    if($data['tag'] == 'select')
    {
        $required = $data['required'] ? 'required':'';

        $res =
        '<tr>
            '.input_hr($data['title']).'
            <td width="40%" class="adm-detail-content-cell-r">
                <'.$data['tag'].' '.$required.' size="10" type="'.$data['type'].'" size="40" name="'.$data['name'].'" value="'.$data['value'].'">';

                foreach ($data['multiselect_values'] as $value)
                {
                    $selected = $value['ID'] == $data['value'] ?
                        'selected' : '';

                    $res .= '<option '
                    .$selected.
                    ' value="'.$value['ID'].'">'.$value['NAME'].'</option>';
                }

                $res .= '<./'.$data['tag'].'.>
            </td>
        </tr>';

        return $res;
    }
}
function inputs($data)
{
    $str = '';
    foreach ($data as $input)
        echo input($input);
}
?>
<style>
    option[selected] {
        color: green;
        border: solid green 1px;
    }

    option:active {
        color: green;
    }
</style>
<?require $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php";?>