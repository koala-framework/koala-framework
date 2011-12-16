<?php
class Kwc_Newsletter_Detail_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();

        $form = Kwc_Abstract_Form::createChildComponentForm($this->getClass(), '-mail');
        $form->setIdTemplate('{component_id}_{id}-mail');
        $this->add($form);

        $this->add(new Kwf_Form_Field_ShowField('create_date', trlKwf('Creation Date')))
            ->setWidth(300);
    }

    protected function _afterInsert(Kwf_Model_Row_Interface $row)
    {
        $row->create_date = date('Y-m-d H:i:s');
        $row->save();
    }
}
