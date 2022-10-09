<?

use Bitrix\Main\Application,
    Godra\Api\Routing\Route,
    Godra\Api\Helpers\Utility\Misc;

// автолоад классов с composer
require_once(Application::getDocumentRoot() . '/local/vendor/autoload.php');

// Заголовки для отдачи Json
Misc::setHeaders('json');

$request = Application::getInstance()->getContext()->getRequest();

if($request->getRequestedPage())
{
    if(strpos($request->getRequestedPage(), 'bitrix') == false)
    {
        $method = Route::toMethod($request->getRequestedPage());
        echo \json_encode($method(), JSON_UNESCAPED_UNICODE);
        die();
    }

}
?>