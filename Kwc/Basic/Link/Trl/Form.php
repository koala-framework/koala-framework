<?php
class Kwc_Basic_Link_Trl_Form_OriginalText extends Kwf_Data_Abstract
{
    public function load($row)
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($row->component_id, array('ignoreVisible'=>true));
        return $c->chained
            ->getComponent()
            ->getRow()
            ->text;
    }
}

class Kwc_Basic_Link_Trl_Form extends Kwc_Abstract_Composite_Form
{
    protected $_createFieldsets = false;
    protected function _initFields()
    {
        $this->add(new Kwf_Form_Field_TextField('text', trlKwf('Linktext')))
            ->setWidth(300);

        $this->add(new Kwf_Form_Field_ShowField('original_text', trlKwf('Original')))
            ->setData(new Kwc_Basic_Link_Trl_Form_OriginalText());

        parent::_initFields();
    }
}
