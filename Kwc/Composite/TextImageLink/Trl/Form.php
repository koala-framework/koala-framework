<?php
class Vpc_Composite_TextImageLink_Trl_Form_OriginalText extends Vps_Data_Abstract
{
    private $_field;
    public function __construct($field)
    {
        $this->_field = $field;
    }

    public function load($row)
    {
        $c = Vps_Component_Data_Root::getInstance()->getComponentByDbId($row->component_id, array('ignoreVisible'=>true));
        return $c->chained
            ->getComponent()
            ->getRow()
            ->{$this->_field};
    }
}

class Vpc_Composite_TextImageLink_Trl_Form extends Vpc_Abstract_Composite_Trl_Form
{
    protected function _initFields()
    {
        $this->add(new Vps_Form_Field_TextField('title', trlVps('Title')));
        $this->add(new Vps_Form_Field_ShowField('original_title', trlVps('Original')))
            ->setData(new Vpc_Composite_TextImageLink_Trl_Form_OriginalText('title'));

        $this->add(new Vps_Form_Field_TextField('teaser', trlVps('Teaser')));
        $this->add(new Vps_Form_Field_ShowField('original_teaser', trlVps('Original')))
            ->setData(new Vpc_Composite_TextImageLink_Trl_Form_OriginalText('teaser'));

        parent::_initFields();
    }
}
