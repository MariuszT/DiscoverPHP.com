<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initRoutes()
    {
        $this->bootstrap('FrontController');
        $frontController = $this->getResource('FrontController');

        $this->bootstrap('Locale');
        $locale = $this->getResource('Locale');

        $router = $frontController->getRouter();
        $router->removeDefaultRoutes();

        $router->addRoute(
            'functions',
            new Zend_Controller_Router_Route(
                ':language/:function',
                array(
                    'controller' => 'function',
                    'action'     => 'function',
                    'language'   => $locale->getLanguage(),
                    'function'   => ''
                ),
                array(
                    'language' => '\w+',
                    'function' => '\w+'
                )
            )
        );

        $staticPages = array(
            'genesis',
            'source',
            'contact'
        );

        foreach ($staticPages AS $staticPage) {
            $router->addRoute(
                $staticPage,
                new Zend_Controller_Router_Route(
                    ':language/@' . $staticPage,
                    array(
                        'controller' => 'page',
                        'action'     => $staticPage,
                        'language'   => $locale->getLanguage()
                    ),
                    array(
                        'language' => '\w+'
                    )
                )
             );
        }

        $router->addRoute(
            'default',
            new Zend_Controller_Router_Route(
                ':language',
                array(
                    'controller' => 'index',
                    'action'     => 'index'
                ),
                array(
                    'language' => '\w+',
                )
            )
        );
    }

    protected function _initConfig()
    {
        $config = new Zend_Config($this->getOptions(), true);
        Zend_Registry::set('config', $config);
    }

    protected function _initFunctions()
    {
        Zend_Registry::set(
            'functions',
            new Zend_Config_Ini(Zend_Registry::get('config')->functions->path)
        );
    }

    protected function _initLanguages()
    {
        Zend_Registry::set(
            'languages',
            new Zend_Config_Ini(Zend_Registry::get('config')->languages->path)
        );
    }

    protected function _initViews()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');

        $this->bootstrap('Locale');
        $locale = $this->getResource('Locale');

        $view->addHelperPath(
            APPLICATION_PATH . '/views/helpers/',
            'Helper_'
        );

        $view->doctype('XHTML5');
        $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=utf-8');
        $view->headTitle()->setSeparator(' - ');
        $view->headTitle('Discover PHP');

        $view->language = $locale->getLanguage();
    }

    protected function _initNavigation()
    {
        $this->bootstrap('layout');
        $layout = $this->getResource('layout');
        $view = $layout->getView();

        $navigation = new Zend_Navigation(Application_Model_Function::buildNavigation());

        Zend_Registry::set('navigation', $navigation);

        $view->navigation($navigation);
    }
}
