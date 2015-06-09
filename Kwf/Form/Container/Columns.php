<?php
/**
 * @package Form
 */
class Kwf_Form_Container_Columns extends Kwf_Form_Container_Abstract
{
    public function __construct($name = null)
    {
        $this->fields = new Kwf_Collection_FormFields(null, 'Kwf_Form_Container_Column');
        parent::__construct($name);
        $this->setLayout('column');
        $this->setBorder(false);
        $this->setBaseCls('x2-plain');
    }

    public function getTemplateVars($values, $fieldNamePostfix = '', $idPrefix = '')
    {
        $ret = parent::getTemplateVars($values, $fieldNamePostfix, $idPrefix);
        $ret['preHtml'] = ''; // damit ein div ausgegeben wird
        $ret['postHtml'] = '<div class="clear"></div>';
        return $ret;
    }
}
