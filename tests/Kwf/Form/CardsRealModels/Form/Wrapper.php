<?php
class Kwf_Form_CardsRealModels_Form_Wrapper extends Kwf_Form
{
    protected $_modelName = 'Kwf_Form_CardsRealModels_Model_WrapperModel';

    protected function _init()
    {
        parent::_init();

        $this->add(new Kwf_Form_Field_TextField('wrappertext', 'Wrappertext'));

        $services = new Kwf_Form_CardsRealModels_Model_WrapperTable();
        $cards = $this->add(new Kwf_Form_Container_Cards('type', trlKwf('Type')));

        $cards->getCombobox()->setAllowBlank(false);

        $form = new Kwf_Form_CardsRealModels_Form_Firstname();
        $card = $cards->add();
        $card->setTitle("Firstname");
        $card->setName("sibfirst");
        $card->add($form);

        $form = new Kwf_Form_CardsRealModels_Form_Lastname();
        $card = $cards->add();
        $card->setTitle("Lastname");
        $card->setName("siblast");
        $card->add($form);

    }

}
