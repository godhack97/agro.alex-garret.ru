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

    public function gen_token()
    {
        if (function_exists('com_create_guid') === true)
            return trim(com_create_guid(), '{}');

        return sprintf(
            '%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(16384, 20479),
            mt_rand(32768, 49151),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535)
        );
    }

    public function preAuthByCookieHash()
    {

        $token = random_bytes(15);
        //echo bin2hex($token);


        //$this->gen_token();

        $cookie_login = ${\COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_LOGIN"};
        $cookie_md5pass = ${\COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_UIDH"};

        $this->cuser->LoginByHash($cookie_login, $cookie_md5pass);

        Context::getCurrent()->getResponse()->writeHeaders();
    }

    /**
     * Get header Authorization
     * */
    private function getAuthorizationHeader(){
        $headers = null;
        if (isset($_SERVER['Authorization']))
        {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION']))
        {
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers'))
        {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));

            if (isset($requestHeaders['Authorization']))
                $headers = trim($requestHeaders['Authorization']);
        }
        return $headers;
    }
    /**
     * get access token from header
     * */
    private function getBearerToken() {
        $headers = $this->getAuthorizationHeader();
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

}
?>