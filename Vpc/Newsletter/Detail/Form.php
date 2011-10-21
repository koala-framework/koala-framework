<?php
class Vpc_Newsletter_Detail_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();

        $form = Vpc_Abstract_Form::createChildComponentForm($this->getClass(), '-mail');
        $form->setIdTemplate('{component_id}_{id}-mail');
        $this->add($form);

        $this->add(new Vps_Form_Field_ShowField('create_date', trlVps('Creation Date')))
            ->setWidth(300);
    }

    protected function _beforeInsert(Vps_Model_Row_Interface $row)
    {
        $row->create_date = date('Y-m-d H:i:s');
    }

}
