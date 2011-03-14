<?php

class Application_Model_Function
{

    public static function issetFunction($name)
    {
        return isset(Zend_Registry::get('functions')->$name);
    }

    public static function issetFunctionRedirect($name)
    {
        if (isset(Zend_Registry::get('functions')->$name) AND
            isset(Zend_Registry::get('functions')->$name->redirect)) {
            return Zend_Registry::get('functions')->$name->redirect;
        }

        return FALSE;
    }

    public static function buildNavigation()
    {
        $config = array();

        foreach (Zend_Registry::get('functions') AS $elementName => $function) {
            if ($function->menu == TRUE) {

                if (isset($function->redirect)) {
                    $elementLink = $function->redirect;
                    $anchor = $elementName;
                } else {
                    $elementLink = $elementName;
                    $anchor = $elementName;
                }

                $config[] = array(
                    'label'      => $elementName,
                    'title'      => $elementName,
                    'module'     => 'default',
                    'controller' => 'function',
                    'action'     => 'function',
                    'route'      => 'functions',
                    'params'     => array('function' => $elementLink),
                    'anchor'     => $anchor
                );
            }
        }

        return $config;
    }
}

