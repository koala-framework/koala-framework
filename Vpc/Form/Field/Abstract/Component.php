<?php
abstract class Vpc_Form_Field_Abstract_Component extends Vpc_Abstract
{
    private $_formField;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vps_Component_FieldModel';
        $ret['flags']['formField'] = true;
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $form = $this->_getForm();
        $postData = array();
        if ($form->getComponent()->isProcessed()) {
            //kann nicht processed sein wenn paragraphs der form im backend bearbeitet werden
            $postData = $form->getComponent()->getPostData();
        }
        $fieldVars = $this->getFormField()->getTemplateVars($postData);
        $dec = Vpc_Abstract::getSetting($form->componentClass, 'decorator');
        if ($dec && is_string($dec)) {
            $dec = new $dec();
            $fieldVars = $dec->processItem($fieldVars);
        }
        $ret = array_merge($ret, $fieldVars);
        return $ret;
    }

    private function _getForm()
    {
        $ret = $this->getData();
        while ($ret && !is_instance_of($ret->componentClass, 'Vpc_Form_Component')) {
            $ret = $ret->parent;
        }
        return $ret;
    }

    /**
     * @return Vps_Form_Field_Abstract
    */
    abstract protected function _getFormField();

    /**
     * @return Vps_Form_Field_Abstract
    */
    public final function getFormField()
    {
        if (!isset($this->_formField)) {
            $this->_formField = $this->_getFormField();
        }
        return $this->_formField;
    }
}
