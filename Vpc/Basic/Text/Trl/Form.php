<?php
class Vpc_Basic_Text_Trl_Form_OriginalText extends Vps_Data_Abstract
{
    public function load($row)
    {
        $c = Vps_Component_Data_Root::getInstance()->getComponentByDbId($row->id);
        return $c->chained->getComponent()->getRow()->content;
    }
}

class Vpc_Basic_Text_Trl_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(Vpc_Abstract_Form::createChildComponentForm($this->getClass(), "-text"));

        $fs = $this->add(new Vps_Form_Container_FieldSet(trlVps("Original")));

        $fs->add(new Vps_Form_Field_ShowField('original_text'))
            ->setHideLabel(true)
            ->setData(new Vpc_Basic_Text_Trl_Form_OriginalText());

        if (!$this->getModel()) {
            $this->setModel(new Vps_Model_FnF());
            $this->setCreateMissingRow(true);
        }
    }
}
