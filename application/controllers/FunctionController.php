<?php

class FunctionController extends Zend_Controller_Action
{
    public function indexAction()
    {
        // action body
    }

    public function functionAction()
    {
        if (Application_Model_Function::issetFunction($this->_getParam('function')) === FALSE) {
            $this->_redirect('/')->setRedirectExit();
        }

        if (($redirect = Application_Model_Function::issetFunctionRedirect($this->_getParam('function'))) !== FALSE) {
            $this->_redirect($redirect)->setRedirectExit();
        }


        $form = new Application_Model_FunctionForm();
        $forms = $form->buildForms();

        $this->view->forms = $forms;

        if (isset(Zend_Registry::get('functions')->{$this->_getParam('function')}->title)) {
            $this->view->headTitle(Zend_Registry::get('functions')->{$this->_getParam('function')}->title)->enableTranslation();
        } else {
            $this->view->headTitle($this->_getParam('function'));
        }
    }
}