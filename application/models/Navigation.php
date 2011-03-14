<?php

class Application_Model_Navigation
{

    public function build()
    {
        $config = array();

        foreach(Zend_Registry::get('functions') AS $elementName => $function) {
            if($function->menu == TRUE) {

                if(isset($function->redirect)) {
                    $elementLink = $function->redirect;
                    $anchor = $elementName;
                }
                else {
                    $elementLink = $elementName;
                    $anchor = '';
                }

                $config[] = array(
                    'label'      => $elementName,
                    'title'      => $elementName,
                    'module' => 'default',
                    'controller' => 'function',
                    'action' => 'function',
                    'route' => 'functions',
                    'params' => array('function' => $elementLink),
                    'anchor' => $anchor
                );
            }
        }

        return $config;
    }
}

