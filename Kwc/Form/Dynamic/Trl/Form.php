<?php
class Kwc_Form_Dynamic_Trl_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $fs = new Kwf_Form_Container_FieldSet(trlKwf('Form Properties'));
        $fs->add(new Kwf_Form_Field_TextField('subject', trlKwf('E-Mail Subject')))
            ->setWidth(400);
        $fs->add(new Kwf_Form_Field_TextField('submit_caption', trlKwf('Submit Caption')))
            ->setWidth(400);
        $this->add($fs);

        $hiddenField = new Kwf_Form_Field_Hidden('send_confirm_mail');
        $hiddenField->setData(new Kwc_Form_Dynamic_Trl_Data());
        $cards = new Kwf_Form_Container_Cards();
        $cards->setCombobox($hiddenField);

        $card = $cards->add(new Kwf_Form_Container_Card());
        $card->setName('0');

        $card = $cards->add(new Kwf_Form_Container_Card());
        $card->setName('1');

        $fs = $card->add(new Kwf_Form_Container_FieldSet(trlKwf('Send copy to User')));
        $fs->add(new Kwf_Form_Field_TextField('confirm_subject', trlKwf('Subject')))
            ->setWidth(300)
            ->setAllowBlank(false);
        $this->add($cards);
    }
}

class Kwc_Form_Dynamic_Trl_Data extends Kwf_Data_Abstract
{
    public function load($row, array $info = array())
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($row->component_id, array('ignoreVisible'=>true));
        if ($c) {
            return $c->chained->getComponent()->getRow()->send_confirm_mail;
        } else {
            return '0';
        }
    }

    public function save(Kwf_Model_Row_Interface $row, $data)
    {
    }
}
