<?php
class Vps_Form_CardsRealModels_Form_Wrapper extends Vps_Form
{
    protected $_modelName = 'Vps_Form_CardsRealModels_Model_WrapperModel';

    protected function _init()
    {
        parent::_init();

        $this->add(new Vps_Form_Field_TextField('wrappertext', 'Wrappertext'));

        $services = new Vps_Form_CardsRealModels_Model_WrapperTable();
        $cards = $this->add(new Vps_Form_Container_Cards('type', trlVps('Type')));

        $cards->getCombobox()->setAllowBlank(false);

        $form = new Vps_Form_CardsRealModels_Form_Firstname();
        $card = $cards->add();
        $card->setTitle("Firstname");
        $card->setName("sibfirst");
        $card->add($form);

        $form = new Vps_Form_CardsRealModels_Form_Lastname();
        $card = $cards->add();
        $card->setTitle("Lastname");
        $card->setName("siblast");
        $card->add($form);

    }

}
