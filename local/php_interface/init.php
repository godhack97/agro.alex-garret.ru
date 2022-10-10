<?

use Bitrix\Main\Application,
    Godra\Api\Routing\Route,
    Godra\Api\Helpers\Utility\Misc,
    Godra\Api\Helpers\Utility\Errors;

// автолоад классов с composer
require_once(Application::getDocumentRoot() . '/local/vendor/autoload.php');

$requestPage = Application::getInstance()->getContext()->getRequest()->getRequestedPage();

if(Misc::checkRequestPage($requestPage))
{
    $method = Route::toMethod($requestPage);

    // Заголовки для отдачи Json
    Misc::setHeaders('json');
    Misc::setHeaders('200');
    Misc::setHeaders('mandatory');

    echo \json_encode( is_callable($method) ? $method() : Errors::notMethod(), JSON_UNESCAPED_UNICODE);

    exit;
}
/*
Пример пост регистрации

fetch('/api/register',{
    method: 'post',
      body: JSON.stringify({
      inn: 'inn',
      login: 'login',
      company_name: 'company_name',
      region: 'region',
      phone: 'phone',
      email: 'email',
    })
  })

*/
?>