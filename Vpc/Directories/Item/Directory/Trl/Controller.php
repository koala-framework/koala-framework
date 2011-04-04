<?php
class Vpc_Directories_Item_Directory_Trl_Controller extends Vps_Controller_Action_Auto_Vpc_Grid
{
    protected $_buttons = array(
        'save',
        'reload',
    );

    protected $_editDialog = array(
        'width' =>  500,
        'height' =>  400
    );
    protected $_hasComponentId = false; //component_id nicht speichern

    protected $_paging = 25;

    public function preDispatch()
    {
        $this->setModel(new Vpc_Directories_Item_Directory_Trl_AdminModel(array(
            'proxyModel' => Vpc_Abstract::createChildModel(
                Vpc_Abstract::getSetting($this->_getParam('class'), 'masterComponentClass')
            ),
            'trlModel' => Vpc_Abstract::createChildModel($this->_getParam('class')),
        )));
        parent::preDispatch();
        $url = Vpc_Admin::getInstance($this->_getParam('class'))->getControllerUrl('Form');
        $this->_editDialog['controllerUrl'] = $url;
    }

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Vps_Grid_Column('id'));

        //shows editDialog
        $this->_columns->add(new Vps_Grid_Column_Button('properties', ' ', 20))
            ->setButtonIcon('/assets/silkicons/newspaper.png')
            ->setTooltip(trlVps('Properties'));

        $extConfig = Vpc_Admin::getInstance($this->_getParam('class'))->getExtConfig();
        $extConfig = $extConfig['items'];
        $i=0;
        foreach ($extConfig['contentEditComponents'] as $ec) {
            $name = Vpc_Abstract::getSetting($ec['componentClass'], 'componentName');
            $icon = Vpc_Abstract::getSetting($ec['componentClass'], 'componentIcon');
            $this->_columns->add(new Vps_Grid_Column_Button('edit_'.$i, ' ', 20))
                ->setColumnType('editContent')
                ->setEditComponentClass($ec['componentClass'])
                ->setEditType($ec['type'])
                ->setEditIdTemplate($ec['idTemplate'])
                ->setEditComponentIdSuffix($ec['componentIdSuffix'])
                ->setButtonIcon((string)$icon)
                ->setTooltip(trlVps('Edit {0}', $name));
            $i++;
        }
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('component_id', $this->_getParam('componentId'));
        return $ret;
    }

    protected function _getRowById($id)
    {
        if (!$id) return null;
        $s = new Vps_Model_Select();
        $s->whereEquals($this->_model->getPrimaryKey(), $id);
        $s->whereEquals('component_id', $this->_getParam('componentId'));
        return $this->_model->getRow($s);
    }
}
