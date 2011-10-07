<?php
class Kwc_Composite_TextImageLink_Trl_Form_OriginalText extends Kwf_Data_Abstract
{
    private $_field;
    public function __construct($field)
    {
        $this->_field = $field;
    }

    public function load($row)
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($row->component_id, array('ignoreVisible'=>true));
        return $c->chained
            ->getComponent()
            ->getRow()
            ->{$this->_field};
    }
}

class Kwc_Composite_TextImageLink_Trl_Form extends Kwc_Abstract_Composite_Trl_Form
{
    protected function _initFields()
    {
        $this->add(new Kwf_Form_Field_TextField('title', trlKwf('Title')));
        $this->add(new Kwf_Form_Field_ShowField('original_title', trlKwf('Original')))
            ->setData(new Kwc_Composite_TextImageLink_Trl_Form_OriginalText('title'));

        $this->add(new Kwf_Form_Field_TextField('teaser', trlKwf('Teaser')));
        $this->add(new Kwf_Form_Field_ShowField('original_teaser', trlKwf('Original')))
            ->setData(new Kwc_Composite_TextImageLink_Trl_Form_OriginalText('teaser'));

        parent::_initFields();
    }
}
