<?php
class Kwc_Directories_Item_Directory_Trl_FormController extends Kwf_Controller_Action_Auto_Form
{
    protected $_buttons = array();
    protected $_permissions = array('save', 'add');

    public function preDispatch()
    {
        parent::preDispatch();
                                                            //idSeparator dynam. holen?
        $this->_form->setId($this->_getParam('componentId').'_'.$this->_getParam('id'));
        $this->_form->setCreateMissingRow(true);
    }

    public function _initFields()
    {
        $class = $this->_getParam('class');

        $this->_form->setModel(new Kwc_Directories_Item_Directory_Trl_AdminModel(array(
            'proxyModel' => Kwc_Abstract::createChildModel(
                Kwc_Abstract::getSetting($this->_getParam('class'), 'masterComponentClass')
            ),
            'trlModel' => Kwc_Abstract::createChildModel($this->_getParam('class')),
        )));

        $detailClasses = Kwc_Abstract::getChildComponentClasses($class, 'detail');
        $forms = array();
        foreach ($detailClasses as $key => $detailClass) {
            $formClass = Kwc_Admin::getComponentClass($detailClass, 'Form');
            $form = new $formClass($key, $detailClass, $class);
            $form->setIdTemplate($this->_getParam('componentId').'_{0}');
            $form->setCreateMissingRow(true);
            $form->setModel(Kwc_Abstract::createChildModel($class));
            $forms[$key] = $form;
        }

        if (count($forms) == 1) {
            $this->_form->add(reset($forms));
        } else {
            $cards = $this->_form->add(new Kwf_Form_Container_Cards('component', trlKwf('Type')))
                ->setComboBox(new Kwf_Form_Field_Hidden('component'));
            $cards->getCombobox()
                ->setWidth(250)
                ->setListWidth(250)
                ->setAllowBlank(false);
            foreach ($forms as $key => $form) {
                $card = $cards->add();
                $card->add($form);
                $card->setTitle(Kwf_Trl::getInstance()->trlStaticExecute(Kwc_Abstract::getSetting($form->getClass(), 'componentName')));
                $card->setName($key);
                $card->setNamePrefix($key);
            }
            $cards->getCombobox()->getData()->cards = $cards->fields;
        }

        $classes = Kwc_Abstract::getChildComponentClasses($class);
        foreach ($classes as $class) {
            $formName = Kwc_Admin::getComponentClass($class, 'ItemEditForm');
            if ($formName) {
                $this->_form->add(new $formName('detail', $class, $this->_getParam('componentId')));
            }
        }
    }
}
