<?php

class Application_Form_Function extends Zend_Form
{
    private $_name;
    private static $counter = 0;

    public function init()
    {
        $this->setAction('')->setMethod('post');

        $source = $this->createElement('textarea', 'source', array('label' => $this->getView()->translate('source data for') . ' ' . $this->_name, 'rows' => 6, 'cols' => 40, 'id' => 'source_' . self::$counter));
        $source->addValidator('stringLength', false, array(1, 5000))
        ->addDecorator(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'source-row'))
        ->setRequired(true);

        $result = $this->createElement('textarea', 'result', array('label' => $this->getView()->translate('result'), 'rows' => 6, 'cols' => 40, 'id' => 'result_' . self::$counter, 'helper' => 'FormTextarea'))
        ->addDecorator(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'result-row'));

        $submit = $this->createElement('submit', 'submit_' . $this->_name, array('label' => $this->getView()->translate('use') . ' ' . $this->_name))
        ->removeDecorator('DtDdWrapper')
        ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'submit'));
        
        $this->addElements(array($source, $result, $submit))->setAttrib('id', 'form-' . $this->_name);

        self::$counter++;
    }

    public function addCopyButton($name)
    {
        $submitCopy = $this->createElement('submit', 'copy_' . $name, array('label' => $this->getView()->translate('copy to') . ' ' . $name, 'onClick' => 'this.form.action=\'#' . $name . '\';return true;'))
        ->removeDecorator('DtDdWrapper')
        ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'submitCopy'));
        $this->addElement($submitCopy);
    }

    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->_name;
    }
}

