<?
use \Bitrix\Main\Context;
use \Bitrix\Main\Application;
use \Bitrix\Main\Web\Cookie;

class utmClass
{
    private $utm;
    private $request;
    private $cookie;
    private static $cookieVal;
    const UTM_PROP_ID = 8;

    function __construct() {
        $this->utm = false;
        $this->request = Context::getCurrent()->getRequest();
    }

    public function detectUtmSource()
    {
        $this->utm = $this->request->get("utm_source");

        if (!empty($this->utm) && (!$this->request->getCookie("utm_source") || $this->utm != $this->request->getCookie("utm_source"))) {
            $this->cookie = new Cookie("utm_source", $this->utm, time() + 86400);
            $this->cookie->setDomain($_SERVER['SERVER_NAME']);
            Application::getInstance()->getContext()->getResponse()->addCookie($this->cookie);
        }
    }

    public static function checkCookie(&$arUserResult, $request, &$arParams, &$arResult)
    {
        self::$cookieVal = Context::getCurrent()->getRequest()->getCookie("utm_source");

        if (!empty(self::$cookieVal) && empty($arUserResult['ORDER_PROP'][self::UTM_PROP_ID])) {
            $arUserResult['ORDER_PROP'][self::UTM_PROP_ID] = self::$cookieVal;
        }
    }
}
