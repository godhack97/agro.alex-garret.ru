<?
namespace Godra\Api\Core;

use Bitrix\Main\Application,
    Godra\Api\Routing\Route,
    Godra\Api\Core\Events,
    Godra\Api\Helpers\Utility\Misc,
    Godra\Api\Helpers\Utility\Errors,
    Godra\Api\Helpers\Auth\Authorisation;

class Init
{
    public static function run()
    {
        // переменная ошибок апи
        global $API_ERRORS;

        // Инициализация событий
        Events::init();
        (new Authorisation)->preAuthByCookieHash();
        $requestPage = Application::getInstance()->getContext()->getRequest()->getRequestedPage();

        if(Misc::checkRequestPage($requestPage))
        {
            $object_arr = (new Route())->toMethod($requestPage);
            $method = $object_arr[1];

            \class_exists($object_arr[0]) ?
                $result = (new $object_arr[0]())->$method():
                $API_ERRORS[] = 'Метод не существует';

            $is_err = count($API_ERRORS);

            // Заголовки для отдачи Json
            Misc::setHeaders('json');
            Misc::setHeaders('mandatory');
            $is_err ? Misc::setHeaders('500') : Misc::setHeaders('200');


            if($method == 'getMap')
            {
                Misc::setHeaders('200');
            echo \json_encode($result ?: $API_ERRORS/*, JSON_UNESCAPED_UNICODE*/);
            }
            else
        echo \json_encode( $is_err ? $API_ERRORS : $result/*, JSON_UNESCAPED_UNICODE*/);

            die();
        }
    }
}
?>