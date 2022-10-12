<?
namespace Godra\Api\Helpers\Auth;

use     \Bitrix\Main\Context,
        \Bitrix\Main\UserTable,
        \Godra\Api\Helpers\Utility\Misc;

class Authorisation extends Base
{
    protected static $data_rows = [
        'login',
        'password'
    ];

    public static function isAuth()
    {
        global $USER;
        if (!is_object($USER)) $USER = new \CUser;

        return (bool) $USER->IsAuthorized();
    }

    public static function authByPassword()
    {
        global $USER;
        if (!is_object($USER)) $USER = new \CUser;

        $data = Misc::getPostDataFromJson();

        if($USER->IsAuthorized())
            $result['errors'][] = 'Вы уже авторизованы';
        else
        {
            // попытка авторизаци
            $auth = $USER->Login(self::getDataByLogin($data['login']), $data['password'], "Y", "Y");

            // проверка полей
            foreach (self::$data_rows as $row)
                if(!$data[$row])
                    $result['errors'][] = 'Заполните '.$row;

            // ошибки авторизации
            if($auth['TYPE'] == 'ERROR')
                $result['errors'][] = str_replace('<br>', '', $auth['MESSAGE']);

            Context::getCurrent()->getResponse()->writeHeaders();
        }

        return $result;
    }

    public static function preAuthByCookieHash()
    {
        global $USER;
        if (!is_object($USER)) $USER = new \CUser;

        $cookie_login = ${\COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_LOGIN"};
        $cookie_md5pass = ${\COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_UIDH"};

        $USER->LoginByHash($cookie_login, $cookie_md5pass);

        Context::getCurrent()->getResponse()->writeHeaders();
    }

}
?>