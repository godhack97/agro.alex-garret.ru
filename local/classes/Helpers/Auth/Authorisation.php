<?
namespace Godra\Api\Helpers\Auth;

use     \Bitrix\Main\Context,
        \Bitrix\Main\UserTable,
        \Godra\Api\Helpers\Utility\Misc;

class Authorisation extends Base
{
    protected $data_rows = [
        'login',
        'password'
    ];

    public function isAuth()
    {
        return (bool) $this->cuser->IsAuthorized();
    }

    public function authByPassword()
    {
        if($this->cuser->IsAuthorized())
            $result['errors'][] = 'Вы уже авторизованы';
        else
        {
            // попытка авторизаци
            $auth = $this->cuser->Login($this->getDataByLogin($this->data['login']), $this->data['password'], "Y", "Y");

            // проверка полей
            foreach ($this->data_rows as $row)
                if(!$this->data[$row])
                    $result['errors'][] = 'Заполните '.$row;

            // ошибки авторизации
            if($auth['TYPE'] == 'ERROR')
                $result['errors'][] = str_replace('<br>', '', $auth['MESSAGE']);

            Context::getCurrent()->getResponse()->writeHeaders();
        }

        return $result;
    }

    public function preAuthByCookieHash()
    {
        $cookie_login = ${\COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_LOGIN"};
        $cookie_md5pass = ${\COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_UIDH"};

        $this->cuser->LoginByHash($cookie_login, $cookie_md5pass);

        Context::getCurrent()->getResponse()->writeHeaders();
    }

}
?>