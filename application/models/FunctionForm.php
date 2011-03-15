<?php

class Application_Model_FunctionForm
{

    public function buildForms()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $forms = array();

        if (isset(Zend_Registry::get('functions')->{$request->getParam('function')}->contains)) {
            foreach (Zend_Registry::get('functions')->{$request->getParam('function')}->contains AS $function) {
                $form = new Application_Form_Function(array('name' => $function));

                $forms[] = array(
                    'form'        => $form,
                    'description' => Zend_Registry::get('functions')->$function->description
                );

                if ($request->isPost()) {

                    if ($this->_checkCopyPost($form, $function)) {
                        $formData = $request->getPost();
                        $tmp = $formData['source'];
                        $formData['source'] = $formData['result'];
                        $formData['result'] = $tmp;
                        if ($form->isValid($formData)) {
                            $form->getElement('source')->setValue($formData['source']);
                            $form->getElement('result')->setValue($this->_functionExec($function, $formData['source']));
                            $this->_addCopyButton($form, $function);
                        } else {
                            $form->getElement('result')->setValue('');
                        }
                    } else if ($this->_checkPost($form, $function)) {
                        $form->getElement('result')->setValue($this->_functionExec($function));
                        $this->_addCopyButton($form, $function);
                    } else {
                        $form->getElement('result')->setValue('');
                    }
                }
            }
        } else {
            $forms[] = array(
                'form'        => new Application_Form_Function(array('name' => $request->getParam('function'))),
                'description' => Zend_Registry::get('functions')->{$request->getParam('function')}->description
            );

            if ($request->isPost()) {

                if ($this->_checkPost($forms[0]['form'])) {
                    $forms[0]['form']->getElement('result')->setValue($this->_functionExec());
                } else {
                    $forms[0]['form']->getElement('result')->setValue('');
                }
            }
        }

        return $forms;
    }

    private function _checkPost(&$form, $function = NULL)
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();

        if ($function == NULL) {
            $function = $request->getParam('function');
        }

        $formData = $request->getPost();

        if (isset($formData['submit_' . $function])) {
            if ($form->isValid($formData)) {
                return TRUE;
            }
        }

        return FALSE;
    }

    private function _checkCopyPost(&$form, $function)
    {
        $formData = Zend_Controller_Front::getInstance()->getRequest()->getPost();

        if (isset($formData['copy_' . $function])) {
            return TRUE;
        }

        return FALSE;
    }

    private function _addCopyButton(&$form, $function)
    {
        if (isset(Zend_Registry::get('functions')->$function->inverse)) {
            $form->addCopyButton(Zend_Registry::get('functions')->$function->inverse);
        }
    }

    private function _functionExec($function = NULL, $content = '')
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();

        if ($function == NULL) {
            $function = $request->getParam('function');
        }

        if ($content == '') {
            $formData = $request->getPost();
            $content = $formData['source'];
        }

        $translator = Zend_Registry::get('Zend_Translate');

        $adapter = $translator->getAdapter();

        switch ($function) {
            case 'explode':
                ob_start();
                var_dump(explode(' ', $content));
                $dump = ob_get_contents();
                ob_end_clean();

                return $dump;
                break;

            case 'base64_decode':
                $try = base64_decode($content, TRUE);

                if ($try === FALSE) {
                    return $adapter->translate('FALSE - this is not base64 encoded data');
                }

                return $try;
                break;

            case 'crc32':
                return sprintf('%u', crc32($content));
                break;

            case 'md4':
                return hash('md4', $content);
                break;

            case 'json_decode':
                $try = json_decode($content);

                if ($try === NULL) {
                    return $adapter->translate('NULL - this is not JSON encoded data');
                }

                return $try;
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
                ob_start();
                var_dump(str_word_count($content, 2));
                $dump = ob_get_contents();
                ob_end_clean();
                return $dump;
                break;

            default:
                if (!function_exists($function)) {
                    return NULL;
                }
                return $function($content);
                break;
        }
    }
}

