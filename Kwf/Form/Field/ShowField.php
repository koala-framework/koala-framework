<?php
/**
 * @package Form
 */
class Kwf_Form_Field_ShowField extends Kwf_Form_Field_SimpleAbstract
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('showfield');
    }

    public function prepareSave(Kwf_Model_Row_Interface $row, $postData)
    {
    }

    public function validate($row, $postData)
    {
        return array();
    }

    public function getTemplateVars($values, $fieldNamePostfix = '', $idPrefix = '')
    {
        $name = $this->getFieldName();
        $ret = parent::getTemplateVars($values, $fieldNamePostfix, $idPrefix);
        //todo: escapen
        $ret['id'] = $idPrefix.$name.$fieldNamePostfix;
        if ($this->getShowText()) {
            throw new Kwf_Exception("ShowField shows a field of a row, but no static text set by 'setShowText'. Use Kwf_Form_Field_Panel instead.");
        }

        $ret['html'] = '&nbsp;';
        if (isset($values[$name]) && $values[$name] != '') {
            $v = $values[$name];
            if ($this->getTpl() == '{value:nl2br}') {
                $v = nl2br($v);
            }
            $ret['html'] = '<span class="fieldContent">'.$v.'</span>';
        }
        return $ret;
    }

    /**
    * can be used to set a specific renderer
    *
    * e.g '{value:date}'
    * to use the date renderer
    *
    * @param string template
    */
    public function setTpl($tpl)
    {
        $ret = parent::setTpl($tpl);
        return $ret;
    }
}
