<?php
class Kwc_Newsletter_Detail_MailingFormController extends Kwf_Controller_Action_Auto_Form
{
    protected $_model = 'Kwc_Newsletter_Model';
    protected $_permissions = array('save');

    protected function _initFields()
    {
        parent::_initFields();
        $form = $this->_form;
        $form->setLabelWidth(120);

        $combobox = new Kwf_Form_Field_Radio('status', trlKwf('Start newsletter'));
        $combobox->setAllowBlank(false);
        $combobox->setWidth(150);
        $cards = $form->add(new Kwf_Form_Container_Cards());
        $cards->setCombobox($combobox);

        $card = $cards->add();
        $card->setTitle(trlKwf('Now'));
        $card->setName('start');

        $card = $cards->add();
        $card->setTitle(trlKwf('Later'));
        $card->setName('startLater');
        $card->add(new Kwf_Form_Field_DateTimeField('start_date', trlKwf('Date')));

        $form->add(new Kwf_Form_Field_Select('mails_per_minute', trlKwf('Sending speed')))
            ->setValues(array(
                'slow' => trlKwf('Slow'),
                'normal' => trlKwf('Normal'),
                'fast' => trlKwf('Fast')
            ));
    }

    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        parent::_beforeSave($row);
        if ($row->status == 'start') $row->start_date = null;
    }
}
