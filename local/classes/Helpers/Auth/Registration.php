<?
namespace Godra\Api\Helpers\Auth;

use \Bitrix\Main\UserTable,
    \Godra\Api\Helpers\Utility\Misc;

class Registration
{
    protected static $data_rows = [
        'password',
        'password_confirm',
        'login',
        'email',
    ];

    public function registerByPassword()
    {
        $data = Misc::getPostDataFromJson();

        foreach (self::$data_rows as $row)
            if(!isset($data[$row]))
                return ['error' => 'не хватает данных для поля '.$row];

        $arFields = [
            "EMAIL"             => $data['email'],
            "LOGIN"             => $data['login'],
            "ACTIVE"            => "Y",
            "GROUP_ID"          => 1,
            "PASSWORD"          => $data['password'],
            "CONFIRM_PASSWORD"  => $data['confirm_password'],
        ];

        $user = new \CUser;
        $new_user_id = $user->Add($arFields);

        return $new_user_id ?: $new_user_id->LAST_ERROR;
    }
}
?>