<?php
class Kwc_Directories_Item_Directory_Trl_FormController_ComponentData extends Kwf_Data_Table
{
    public function load($row, array $info = array())
    {
        $name = $this->getField();
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($row->component_id, array('ignoreVisible' => true));
        return $c->chained->row->$name;
    }

    public function save(Kwf_Model_Row_Interface $row, $data)
    {
    }
}

class Kwc_Directories_Item_Directory_Trl_FormController extends Kwf_Controller_Action_Auto_Form
{
    protected $_buttons = array();
    protected $_permissions = array('save', 'add');

    public function preDispatch()
    {
        parent::preDispatch();
        $gen = Kwf_Component_Generator_Abstract::getInstance($this->_getParam('class'), 'detail');
        $this->_form->setId($this->_getParam('componentId').$gen->getIdSeparator().$this->_getParam('id'));
        $this->_form->setCreateMissingRow(true);
    }

    public function _initFields()
    {
        $class = $this->_getParam('class');

        $this->_form->setModel(Kwc_Abstract::createChildModel($class));

        $gen = Kwf_Component_Generator_Abstract::getInstance($this->_getParam('class'), 'detail');
        $detailClasses = Kwc_Abstract::getChildComponentClasses($class, 'detail');
        $forms = array();
        foreach ($detailClasses as $key => $detailClass) {
            $formClass = Kwc_Admin::getComponentClass($detailClass, 'Form');
            $form = new $formClass($key, $detailClass, $class);
            $form->setIdTemplate('{0}');
            $form->setCreateMissingRow(true);
            $form->setModel($this->_form->getModel());
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
                ->setAllowBlank(false)
                ->setData(new Kwc_Directories_Item_Directory_Trl_FormController_ComponentData('component'));
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
