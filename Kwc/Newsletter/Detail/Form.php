<?php
class Kwc_Newsletter_Detail_Form extends Kwc_Abstract_Form
{
    protected $_model = 'Kwc_Newsletter_Model';

    public function setId($id)
    {
        $id = substr(strrchr($id, '_'), 1);
        parent::setId($id);
    }

    protected function _initFields()
    {
        parent::_initFields();

        $form = Kwc_Abstract_Form::createChildComponentForm($this->getClass(), '_mail');
        $form->setIdTemplate('{component_id}_{id}_mail');
        $this->add($form);

        $this->add(new Kwf_Form_Field_ShowField('create_date', trlKwf('Creation Date')))
            ->setWidth(300);
    }
}
