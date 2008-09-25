<?php
class Vps_Form_Field_ShowField extends Vps_Form_Field_SimpleAbstract
{
    public function load($row, $postData = array())
    {
        $ret = parent::load($row, $postData);
        $fn = $this->getFieldName();
        $ret[$fn] = nl2br($ret[$fn]);
        return $ret;
    }
    
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('showfield');
    }
    public function prepareSave(Vps_Model_Row_Interface $row, $postData)
    {
    }
    public function getTemplateVars($values, $fieldNamePostfix = '')
    {
        $name = $this->getFieldName();
        $ret = parent::getTemplateVars($values);
        //todo: escapen
        $ret['id'] = $name.$fieldNamePostfix;
        if (!$this->getShowText()) $this->setShowText('&nbsp;');
        $ret['html'] = $this->getShowText();
        return $ret;
    }

    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName' => trlVps('Show field')
        ));
    }
}
