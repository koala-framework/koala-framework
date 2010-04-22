<?php
class Vps_Form_Field_Panel extends Vps_Form_Container_Abstract
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('panel');
        $this->setBaseCls('x-plain');
    }
    public function getTemplateVars($values)
    {
        $ret = parent::getTemplateVars($values);
        if ($this->getHtml()) {
            $ret['html'] = $this->getHtml();
        }
        return $ret;
    }

    protected function _getTrlProperties()
    {
        $ret = parent::_getTrlProperties();
        $ret[] = 'html';
        return $ret;
    }
}
