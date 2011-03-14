<?php

class Discover_Controller_Plugin_Translate
    extends Zend_Controller_Plugin_Abstract
{

    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {

        $pathInfo        = trim($request->getPathInfo(), '/');
        $defaultLanguage = Zend_Controller_Front::getInstance()
        ->getParam('bootstrap')
        ->getResource('locale')
        ->getLanguage();

        if ($pathInfo == '') {
            Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector')->gotoUrlAndExit($defaultLanguage, array('code' => 301));
        }

        $languages = Zend_Registry::get('languages');
        $uriParts = explode('/', $pathInfo);

        if ($uriParts[0] != $defaultLanguage AND !isset(Zend_Registry::get('languages')->$uriParts[0])) {
            if (count($uriParts) == 1) {
                if (isset($_COOKIE['language']) AND isset(Zend_Registry::get('languages')->$uriParts[0])) {
                    $redirectToLanguage = $_COOKIE['language'];
                } else {
                    $redirectToLanguage = $defaultLanguage;
                }
                Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector')->gotoUrlAndExit($redirectToLanguage . '/' . $uriParts[0], array('code' => 301));
            } else {
                if (isset($_COOKIE['language']) AND isset(Zend_Registry::get('languages')->$uriParts[0])) {
                    $uriParts[0] = $_COOKIE['language'];
                } else {
                    $uriParts[0] = $defaultLanguage;
                }

                Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector')->gotoUrlAndExit(implode('/', $uriParts));
            }
        } else {
            if (isset($_GET['setLanguage'])) {
                setcookie('language', $uriParts[0], time() + 155520000, '/'); // five years until now
            } else if (isset($_COOKIE['language']) AND isset(Zend_Registry::get('languages')->$uriParts[0]) AND $_COOKIE['language'] != $uriParts[0]) {
                $uriParts[0] = $_COOKIE['language'];
                Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector')->gotoUrlAndExit(implode('/', $uriParts));
            }

            $language = $uriParts[0];
            $frontController = Zend_Controller_Front::getInstance();
            $router = $frontController->getRouter();
            $router->setGlobalParam('language', $language);

            $translate = NULL;
            foreach (Zend_Registry::get('languages') AS $langShortcut => $langInfo) {

                if ($translate === NULL) {
                    $translate = new Zend_Translate(
                        array(
                            'adapter'        => 'gettext',
                            'content'        => APPLICATION_PATH . '/../languages/' . $langInfo->code . '.mo',
                            'locale'         => $langShortcut,
                            'disableNotices' => TRUE
                        )
                    );
                } else {
                    $translate->addTranslation(
                        array(
                            'adapter'        => 'gettext',
                            'content'        => APPLICATION_PATH . '/../languages/' . $langInfo->code . '.mo',
                            'locale'         => $langShortcut,
                            'disableNotices' => TRUE
                        )
                    );
                }
            }

            $zendLocale = new Zend_Locale();
            $zendLocale->setLocale(Zend_Registry::get('languages')->$language->code);
            Zend_Registry::set('Zend_Locale', $zendLocale);

            $translate->setLocale($zendLocale);

            Zend_Controller_Router_Route::setDefaultTranslator($translate);
            Zend_Form::setDefaultTranslator($translate);
            Zend_Registry::set('Zend_Translate', $translate);
        }
    }
}