<?
use Bitrix\Main\Application,
    Godra\Api\Core\Init;

// автолоад классов с composer
require_once(Application::getDocumentRoot() . '/local/vendor/autoload.php');

$eventManager = \Bitrix\Main\EventManager::getInstance();

$eventManager->addEventHandlerCompatible(
  'main',
  'OnProlog',
  ['Godra\\Api\\Core\\Init', 'run']
);
?>