<?php
class Vps_Form_Field_ShowField extends Vps_Form_Field_SimpleAbstract
{
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
        if ($this->getShowText()) {
            throw new Vps_Exception("ShowField shows a field of a row, but no static text set by 'setShowText'. Use Vps_Form_Field_Panel instead.");
        }

        $ret['html'] = '&nbsp;';
        if (isset($values[$name]) && $values[$name] != '') {
            $ret['html'] = $values[$name];
        }
        return $ret;
    }

    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName' => trlVps('Show field')
        ));
    }
}
