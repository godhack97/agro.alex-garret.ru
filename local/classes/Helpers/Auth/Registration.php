<?
namespace Godra\Api\Helpers\Auth;

use \Godra\Api\Services\Form,
    \Godra\Api\Helpers\Utility\Misc;

class Registration
{
    protected static $data_rows = [
        'company_name',
        'inn',
        'login',
        'region',
        'phone',
        'password',
        'email',
    ];

    public static function registerByPassword()
    {
        $data = Misc::getPostDataFromJson();

        foreach (self::$data_rows as $row)
            if(!isset($data[$row]))
                return ['error' => 'не хватает данных для поля '.$row];

        $arFields = [
            "EMAIL"             => $data['email'],
            "LOGIN"             => $data['login'],
            "ACTIVE"            => "N",
            "GROUP_ID"          => 1,
            "PASSWORD"          => $data['password'],
            "CONFIRM_PASSWORD"  => $data['password'],
        ];

        $user = new \CUser;
        $new_user_id = $user->Add($arFields);

        return $new_user_id ?: $new_user_id->LAST_ERROR;
    }

    public static function registerByForm()
    {
        $data = Misc::getPostDataFromJson();

        return (new Form(REGISTRATION_FORM_SID, $data))->addResult();
    }
}
?>