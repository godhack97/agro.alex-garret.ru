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
        // Инициализация событий
        Events::init();
        Authorisation::preAuthByCookieHash();
        $requestPage = Application::getInstance()->getContext()->getRequest()->getRequestedPage();

        if(Misc::checkRequestPage($requestPage))
        {
            $method = Route::toMethod($requestPage);

            echo \json_encode( is_callable($method) ? $method() : Errors::notMethod(), JSON_UNESCAPED_UNICODE);

            // Заголовки для отдачи Json
            Misc::setHeaders('json');
            Misc::setHeaders('200');
            Misc::setHeaders('mandatory');

            die();
        }
    }
}
?>