<?php
class Kwf_Form_CheckboxFieldsetInCards_TestController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Kwf_Form_CheckboxFieldsetInCards_TestModel';
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');

    protected function _initFields()
    {
        $cards = $this->_form->add(new Kwf_Form_Container_Cards('cards', "Foo"));
        $cards->setCombobox(new Kwf_Form_Field_Radio('cards', 'Foo'));

        $card0 = $cards->add();
        $card0->setName('card1');
        $card0->setTitle('Card1');

        $card1 = $cards->add();
        $card1->setName('card2');
        $card1->setTitle('Card2');

        $fs = $card1->add(new Kwf_Form_Container_FieldSet("Bar"))
            ->setCheckboxToggle(true)
            ->setCheckboxName('fs2');
        $fs->add(new Kwf_Form_Field_TextField("text2", "Text2"))
            ->setAllowBlank(false);

        $card2 = $cards->add();
        $card2->setName('card3');
        $card2->setTitle('Card3');
        $subCards = $card2->add(new Kwf_Form_Container_Cards('subcards', "SubCards"));
        $cb = $subCards->getCombobox();
        $cb->setCls('kwf-test-subcards');
        $subCard1 = $subCards->add();
        $subCard1->setName('subcard1');
        $subCard1->setTitle('subcard1');

        $subCard2 = $subCards->add();
        $subCard2->setName('subcard2');
        $subCard2->setTitle('subcard2');

        $card3 = $cards->add();
        $card3->setName('card4');
        $card3->setTitle('Card4');

        $fs = $card3->add(new Kwf_Form_Container_FieldSet("Bar4"))
            ->setCheckboxToggle(true)
            ->setCheckboxName('fs3');
        $fs->add(new Kwf_Form_Field_TextField("text4", "Text4"))
            ->setAllowBlank(false)
            ->setCls('kwf-test-text4');;

    }

    public function indexAction()
    {
        $config = $this->_form->getProperties();
        if (!$config) { $config = array(); }
        $config['baseParams']['id'] = 1;
        $config = array_merge(
            $config,
            array(
                'controllerUrl' => $this->getRequest()->getPathInfo(),
                'assetsPackage' => new Kwf_Assets_Package_TestPackage('Kwf_Form_CheckboxFieldsetInCards'),
            )
        );
        $this->view->ext('Kwf.Auto.FormPanel', $config, 'Kwf.Test.Viewport');
    }
}

