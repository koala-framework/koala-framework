<?php
class Vps_Form_Field_Static extends Vps_Form_Field_Abstract
{
    //setText

    public function __construct($text, $fieldLabel = null)
    {
        parent::__construct(null, $fieldLabel);
        $this->setXtype('staticfield');
        $this->setText($text);
    }

    protected function _getTrlProperties()
    {
        $ret = parent::_getTrlProperties();
        $ret[] = 'text';
        return $ret;
    }

    public function setFieldLabel($v)
    {
        $this->setLabelSeparator($v ? ':' : '');
        return $this->setProperty('fieldLabel', $v);
    }

    public function getTemplateVars($values, $fieldNamePostfix = '')
    {
        $ret = parent::getTemplateVars($values);
        $ret['id'] = $this->getFieldName().$fieldNamePostfix;
        $cls = $this->getCls();
        $ret['html'] = "<div class=\"$cls\">" . $this->getText() . "</div>";
        return $ret;
    }

}
