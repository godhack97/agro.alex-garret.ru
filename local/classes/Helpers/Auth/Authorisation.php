<?
namespace Godra\Api\Helpers\Auth;

use     \Bitrix\Main\Context,
        \Bitrix\Main\UserTable,
        \Godra\Api\Helpers\Utility\Misc;

use Bitrix\Main\Web\JWT;

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
            $rules = new \Bitrix\Main\Authentication\Policy\RulesCollection;
            // Ставлю время сессии равное времени действия токена
            // $rules->set( 'SESSION_TIMEOUT' , (TOKEN_EXPIRE_SEC/60) );


            // попытка авторизаци
            $auth = $this->cuser->Login($this->getDataByLogin($this->data['login']), $this->data['password'], "Y", "Y");

            if(!$auth)
                $GLOBAL_API[] = 'Проверьте данные авторизации';

            // проверка полей
            foreach ($this->data_rows as $row)
                if(!$this->data[$row])
                    $result['errors'][] = 'Заполните '.$row;

            // ошибки авторизации
            if($auth['TYPE'] == 'ERROR')
                $result['errors'][] = str_replace('<br>', '', $auth['MESSAGE']);

            Context::getCurrent()->getResponse()->writeHeaders();

            $token = [
                'sub'   => $this->cuser->getId(),
                'login' => $this->cuser->getLogin(),
                'iat'   => time(),
                'exp'   => time() + $this->getPoliciesByUserGroup()*60,
            ];

            return [
                'token'   => JWT::encode($token, TOKEN_SECRET_KEY),
                'user_id' => $this->cuser->getId(),
                'premission' => 'superuser',
            ];
        }

        return $result;
    }


    /**
     * Получает время действия сессии группы пользователей с кодом all
     * @return int
     */
    public function getPoliciesByUserGroup()
    {
        global $DB;

        // группа "все пользователи"
        $group_alluser_id = $DB->Query('SELECT ID FROM b_group G WHERE STRING_ID="all"', true)->fetch()['ID'];

        // Правила группы
        if($group_alluser_id)
            $policy = \unserialize(
                $DB->Query('SELECT G.SECURITY_POLICY FROM b_group G WHERE G.ID='.$group_alluser_id)->fetch()['SECURITY_POLICY']
            );

        return $policy['SESSION_TIMEOUT'] ?: 24;

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