<?php
class Vpc_Basic_Link_Trl_Form_OriginalText extends Vps_Data_Abstract
{
    public function load($row)
    {
        $c = Vps_Component_Data_Root::getInstance()->getComponentByDbId($row->component_id, array('ignoreVisible'=>true));
        return $c->chained
            ->getComponent()
            ->getRow()
            ->text;
    }
}

class Vpc_Basic_Link_Trl_Form extends Vpc_Abstract_Composite_Form
{
    protected $_createFieldsets = false;
    protected function _initFields()
    {
        $this->add(new Vps_Form_Field_TextField('text', trlVps('Linktext')))
            ->setWidth(300);

        $this->add(new Vps_Form_Field_ShowField('original_text', trlVps('Original')))
            ->setData(new Vpc_Basic_Link_Trl_Form_OriginalText());

        parent::_initFields();
    }
}
