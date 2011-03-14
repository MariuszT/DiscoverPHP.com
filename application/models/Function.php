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

    private function _buildForms()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $forms = array();

        if (isset(Zend_Registry::get('functions')->{$request->getParam('function')}->contains)) {
            foreach (Zend_Registry::get('functions')->{$request->getParam('function')}->contains AS $function) {
                $form = new Application_Form_Function(array('name' => $function));

                $forms[] = $form;

                if ($request->isPost()) {

                    if ($this->_checkCopyPost($form, $function)) {
                        $formData = $request->getPost();
                        $form->getElement('source')->setValue($formData['result']);
                        $form->getElement('result')->setValue($this->_functionExec($function, $formData['result']));
                        $this->_addCopyButton($form, $function);
                    } else if ($this->_checkPost($form, $function)) {
                        $form->getElement('result')->setValue($this->_functionExec($function));
                        $this->_addCopyButton($form, $function);
                    } else {
                        $form->getElement('result')->setValue('');
                    }
                }
            }
        } else {
            $forms[] = new Application_Form_Function(array('name' => $this->_getParam('function')));

            if ($request->isPost()) {
                if ($this->_checkPost($forms[0])) {
                    $forms[0]->getElement('result')->setValue($this->_functionExec());
                } else {
                    $forms[0]->getElement('result')->setValue('');
                }
            }
        }
    }

    public function functionExec($function, $content)
    {
        switch ($function) {
            case 'explode':
                ob_start();
                var_dump(explode(' ', $content));
                $dump = ob_get_contents();
                ob_end_clean();

                return $dump;
                break;

            case 'md4':
                return hash('md4', $content);
                break;

            case 'sha256':
                return hash('sha256', $content);
                break;

            case 'sha384':
                return hash('sha384', $content);
                break;

            case 'sha512':
                return hash('sha512', $content);
                break;

            case 'str_word_count':
                return str_word_count($content, 2);
                break;

            default:
                if (!function_exists($function)) {
                    return NULL;
                }
                return $function($content);
                break;
        }
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

