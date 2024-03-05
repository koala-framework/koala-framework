<?php
class Kwc_Form_Field_Abstract_Component extends Kwc_Abstract
{
    private $_formField;

    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['flags']['formField'] = true;
        $ret['viewCache'] = true;
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_Form';
        $ret['plugins']['useViewCache'] = 'Kwc_Form_Field_Abstract_UseViewCachePlugin';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $this->getFormField()->trlStaticExecute($this->getData()->getLanguage());
        $form = $this->_getForm();

        //initialize form, sets formName on fields
        $form->getComponent()->getForm();

        if (!($form->getComponent() instanceof Kwc_Form_Component)) {
            if ($form->getComponent() instanceof Kwc_Form_NonAjax_Component) {
                throw new Kwf_Exception("Form component must not be a Kwc_Form_NonAjax_Component");
            }
            throw new Kwf_Exception("Form component must be a Kwc_Form_Component");
        }

        $postData = array();
        $errors = array();
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
        while ($ret && !is_instance_of($ret->componentClass, 'Kwc_Form_Dynamic_Component')) {
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
        return $this->getData()->chained->getComponent()->getFormField();
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
