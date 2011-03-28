<?php
class Vpc_Form_Field_Abstract_Trl_Component extends Vpc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['ownModel'] = 'Vps_Component_FieldModel';
        $ret['flags']['formField'] = true;
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = Vpc_Abstract::getTemplateVars();
        $form = $this->_getForm();
        $postData = array();
        $errors = array();
        if ($form->getComponent()->isProcessed()) {
            //kann nicht processed sein wenn paragraphs der form im backend bearbeitet werden
            $postData = $form->getComponent()->getPostData();
            $errors = $this->_getForm()->getComponent()->getErrors();
        }
        $fieldVars = $this->getFormField()->getTemplateVars($postData);
        $dec = Vpc_Abstract::getSetting($form->componentClass, 'decorator');
        if ($dec && is_string($dec)) {
            $dec = new $dec();
            $fieldVars = $dec->processItem($fieldVars, $errors);
        }
        $ret = array_merge($ret, $fieldVars);
        return $ret;
    }

    private function _getForm()
    {
        $ret = $this->getData();
        while ($ret && !is_instance_of($ret->componentClass, 'Vpc_Form_Dynamic_Trl_Component')) {
            $ret = $ret->parent;
        }
        $ret = $ret->getChildComponent('-form');
        return $ret;
    }

    /**
     * @return Vps_Form_Field_Abstract
    */
    protected function _getFormField()
    {
        $ret = $this->getData()->chained->getComponent()->getFormField();
        $ret->setName($this->getData()->componentId);
        if ($this->getRow()->field_label) $ret->setFieldLabel($this->getRow()->field_label);
        return $ret;
    }

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
