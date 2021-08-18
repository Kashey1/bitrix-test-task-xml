<?
require_once($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/lib/utmClass.php');

use \Bitrix\Main\EventManager;

define('UTM_SOURCE_PROP_ID', 8);

$eventManager = EventManager::getInstance();
$eventManager->addEventHandler("main", "OnProlog", array(new utmClass, "detectUtmSource"));
$eventManager->addEventHandler("sale", "OnSaleComponentOrderProperties", array("utmClass", "checkCookie"));
$eventManager->addEventHandler("main", "CustomMorizoTestTaskEvent", "CustomMorizoTestTaskEventHandler");

function CustomMorizoTestTaskEventHandler(\Bitrix\Main\Event $event)
{
    $arParam = $event->getParameters();
    \Bitrix\Main\Diag\Debug::writeToFile(
        date('m.d.y-H:m:s')." - Успешно записанных в ИБ сущностей ".$arParam['count'],
        "",
        "/local/php_interface/log.txt");
}
