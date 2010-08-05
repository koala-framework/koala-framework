<?php
class Vpc_Directories_Item_Directory_EditButtonsController extends Vpc_Directories_Item_Directory_Controller
{
    protected $_editDialog = array(
        'width' =>  500,
        'height' =>  400,
        'autoForm' => 'Vpc.Directories.Item.Directory.EditFormPanel'
    );

    public function preDispatch()
    {
        parent::preDispatch();
        $url = Vpc_Admin::getInstance($this->_getParam('class'))->getControllerUrl('Form');
        $this->_editDialog['controllerUrl'] = $url;
    }

    protected function _initColumns()
    {
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
}
