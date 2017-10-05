<?php
/**
 * @package Form
 */
class Kwf_Form_Field_Hidden extends Kwf_Form_Field_SimpleAbstract
{
    public function __construct($field_name = null)
    {
        parent::__construct($field_name);
        $this->setXtype('hidden');
    }
    public function getTemplateVars($values, $fieldNamePostfix = '', $idPrefix = '')
    {
        $name = $this->getFieldName();
        $value = (string)$values[$name];

        $ret = parent::getTemplateVars($values, $fieldNamePostfix, $idPrefix);

        $value = Kwf_Util_HtmlSpecialChars::filter($value);
        $name = Kwf_Util_HtmlSpecialChars::filter($name);
        $ret['html'] = "<input type=\"hidden\" ".
                        "name=\"$name$fieldNamePostfix\" value=\"$value\" />";
        return $ret;
    }
}
