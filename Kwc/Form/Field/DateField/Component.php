<?php
class Kwc_Form_Field_DateField_Component extends Kwc_Form_Field_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Form.Datefield');
        $ret['componentIcon'] = new Kwf_Asset('date');
        $ret['assetsAdmin']['dep'][] = 'KwfFormDateTimeField';
        return $ret;
    }

    protected function _getFormField()
    {
        $ret = new Kwf_Form_Field_DateField($this->getData()->componentId);
        $ret->setFieldLabel($this->getRow()->field_label);
        if ($this->getRow()->label_width) $ret->setLabelWidth($this->getRow()->label_width);
        $ret->setDefaultValue($this->getRow()->default_value);
        $ret->setAllowBlank(!$this->getRow()->required);
        $ret->setHideLabel($this->getRow()->hide_label);
        return $ret;
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
        $t = strtotime($row->{$f->getName()});
        $message .= date($this->getData()->trlKwf('Y-m-d'), $t);
        return $message;
    }
}
