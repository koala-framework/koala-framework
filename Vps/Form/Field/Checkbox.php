<?php
class Vps_Form_Field_Checkbox extends Vps_Form_Field_SimpleAbstract
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('checkbox');
        $this->setEmptyMessage(trlVpsStatic("Please mark the checkbox"));
    }

    /**
     * @deprecated
     */
    public function setErrorText()
    {
        throw new Vps_Exception('setErrorText is deprecated, use setEmptyText');
    }

    protected function _getTrlProperties()
    {
        $ret = parent::_getTrlProperties();
        $ret[] = 'boxLabel';
        return $ret;
    }

    public function getTemplateVars($values, $fieldNamePostfix = '')
    {
        $name = $this->getFieldName();
        $value = isset($values[$name]) ? $values[$name] : $this->getDefaultValue();

        $ret = parent::getTemplateVars($values);
        //todo: escapen
        $ret['id'] = str_replace(array('[', ']'), array('_', '_'), $name.$fieldNamePostfix);
        $ret['html'] = "<input type=\"checkbox\" id=\"$ret[id]\" name=\"$name$fieldNamePostfix\" ";
        if ($value) $ret['html'] .= 'checked="checked" ';
        $ret['html'] .= "/>";
        if ($this->getBoxLabel()) {
            $ret['html'] .= ' '.$this->getBoxLabel();
        }
        $ret['html'] .= "<input type=\"hidden\" name=\"$name$fieldNamePostfix-post\" value=\"1\" />";
        return $ret;
    }

    public function processInput($row, $postData)
    {
        $fieldName = $this->getFieldName();
        if (isset($postData[$fieldName.'-post'])) {
            $postData[$fieldName] = (int)isset($postData[$fieldName]);
        }
        return $postData;
    }
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName' => trlVps('Checkbox')
        ));
    }
}
