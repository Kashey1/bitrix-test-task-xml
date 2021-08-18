<?
if (PHP_SAPI !== 'cli') {
    die("CLI");
}

$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__) . '/../../..');

ini_set('display_errors', 1);
ini_set('memory_limit', '512M');

@set_time_limit(0);
@ignore_user_abort(true);

if ($argv) {
    unset($argv[0]);
    parse_str(join('&', $argv), $_REQUEST);
}

require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");

if (!isset($_REQUEST['xml']) || empty($_REQUEST['xml'])) {
    die('XML path is empty!');
}

if (!isset($_REQUEST['item']) || empty($_REQUEST['item'])) {
    die('Parametr item is empty!');
}

if (!file_exists($_SERVER['DOCUMENT_ROOT'].$_REQUEST['xml'])) {
    die('XML '.$_REQUEST['xml'].' file does not exist!');
}

$startTime = new DateTime('now');

$iBlockID = 4;
$deleteCounter = 0;
$addCounter = 0;

// очистка ИБ
if (!CModule::IncludeModule("iblock")) {
    die('IncludeModule iblock fail!');
} else {
    $arSelect = array("ID");
    $arFilter = array("IBLOCK_ID" => $iBlockID);
    $res = CIBlockElement::GetList(array('id' => 'asc'), $arFilter, false, Array(), $arSelect);

    while ($item = $res->GetNext()) {
		if (CIBlockElement::Delete($item['ID']))
            $deleteCounter++;
    }

    if ($deleteCounter > 0)
        echo $deleteCounter." elements removed \n";
}

// парсинг XML
$xml = new XMLReader();
$xml->open($_SERVER['DOCUMENT_ROOT'].$_REQUEST['xml']);

while($xml->read() && $xml->name != $_REQUEST['item']) {
    ;
}

while ($xml->name == $_REQUEST['item']) {
	$xmlNode = new SimpleXMLElement($xml->readOuterXML());

	$iBlockElementFields = array(
        'IBLOCK_SECTION_ID' => false,
        'IBLOCK_ID' => $iBlockID,
        'ACTIVE' => 'Y',
		'NAME' => strval($xmlNode->title),
        'CODE' => Cutil::translit(strval($xmlNode->title), "ru", array("replace_space" => "-", "replace_other" => "-")),
		'PREVIEW_TEXT' => strval($xmlNode->revision->comment),
	);

    $iBlockElement = new CIBlockElement;
    if ($iBlockElementID = $iBlockElement->Add($iBlockElementFields)) {
        echo "Add element with ID = ".$iBlockElementID." \n";
        $addCounter++;
    }

	$xml->next($_REQUEST['item']);
	unset($xmlNode);
	unset($iBlockElement);
	unset($iBlockElementID);
}

if ($addCounter > 0)
    echo "Done. \n".$addCounter." elements add! \n";

$endTime = new DateTime('now');
$interval = $startTime->diff($endTime);
echo $interval->format('%I минут, %S секунд, %f  миллисекунд');

// вызов события
$event = new \Bitrix\Main\Event('main', 'CustomMorizoTestTaskEvent', array(
    'count' => $addCounter,
));

$event->send();
