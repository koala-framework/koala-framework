<?php
class Kwc_Form_Field_Abstract_Trl_Component extends Kwc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['flags']['formField'] = true;
        $ret['viewCache'] = false;
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_Form';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = Kwc_Abstract::getTemplateVars($renderer);
        $this->getFormField()->trlStaticExecute($this->getData()->getLanguage());
        $form = $this->_getForm();

        //initialize form, sets formName on fields
        $form->getComponent()->getForm();

        $postData = array();
        $errors = array();
        if ($form->getComponent()->isProcessed()) {
            //kann nicht processed sein wenn paragraphs der form im backend bearbeitet werden
            $postData = $form->getComponent()->getPostData();
            $errors = $this->_getForm()->getComponent()->getErrors();
        }
        $fieldVars = $this->getFormField()->getTemplateVars($postData);
        $dec = Kwc_Abstract::getSetting($form->componentClass, 'decorator');
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
        while ($ret && !is_instance_of($ret->componentClass, 'Kwc_Form_Dynamic_Trl_Component')) {
            $ret = $ret->parent;
        }
        $ret = $ret->getChildComponent('-form');
        return $ret;
    }

    /**
     * @return Kwf_Form_Field_Abstract
    */
    protected function _getFormField()
    {
        $ret = $this->getData()->chained->getComponent()->getFormField();
        $ret->setName($this->getData()->componentId);
        if ($this->getRow()->field_label) $ret->setFieldLabel($this->getRow()->field_label);
        return $ret;
    }

    /**
     * @return Kwf_Form_Field_Abstract
    */
    public final function getFormField()
    {
        if (!isset($this->_formField)) {
            $this->_formField = $this->_getFormField();
        }
        return $this->_formField;
    }

    /**
     * This function is used to return a human-readable string for this field
     * depending on submited data.
     * @param Kwc_Form_Dynamic_Form_MailRow $row
     * @return string
     */
    public function getSubmitMessage($row)
    {
        $message = '';
        if ($this->getFormField()->getFieldLabel()) {
            $message .= $this->getFormField()->getFieldLabel().': ';
        }

        $message .= $row->{$this->getFormField()->getName()};
        return $message;
    }
}
