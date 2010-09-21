<?php
class Vpc_Directories_Item_Directory_Controller extends Vps_Controller_Action_Auto_Vpc_Grid
{
    protected $_buttons = array(
        'save',
        'delete',
        'reload',
        'add'
    );

    protected $_filters = array('text'=>true);
    protected $_paging = 25;

    public function preDispatch()
    {
        parent::preDispatch();
        if (is_instance_of(Vpc_Abstract::getSetting($this->_getParam('class'), 'extConfig'), 'Vpc_Directories_Item_Directory_ExtConfigEditButtons')) {
            $url = Vpc_Admin::getInstance($this->_getParam('class'))->getControllerUrl('Form');
            $this->_editDialog = array(
                'width' =>  500,
                'height' =>  400,
                'autoForm' => 'Vpc.Directories.Item.Directory.EditFormPanel',
                'controllerUrl' => $url
            );
        }
    }

    protected function _initColumns()
    {
        if (is_instance_of(Vpc_Abstract::getSetting($this->_getParam('class'), 'extConfig'), 'Vpc_Directories_Item_Directory_ExtConfigEditButtons')) {
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
                    ->setButtonIcon($icon->toString(array('arrow')))
                    ->setTooltip(trlVps('Edit {0}', $name));
                $i++;
            }
        }
    }

    protected function _beforeSave(Vps_Model_Row_Interface $row)
    {
        parent::_beforeSave($row);
        if ($row->getModel()->hasColumn('visible') && !$row->visible) {
            $this->_checkRowIndependence($row, trlVps('hide'));
        }
    }

    protected function _beforeDelete(Vps_Model_Row_Interface $row)
    {
        parent::_beforeDelete($row);
        $this->_checkRowIndependence($row, trlVps('delete'));
    }

    private function _checkRowIndependence(Vps_Model_Row_Interface $row, $msgMethod)
    {
        $c = Vps_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->_getParam('componentId'));
        // wenn zB Newsletter statisch in root erstellt wurde, gibts kein visible
        if (!$c) {
            //wenn seite offline ist ignorieren
            //  ist nicht natürlich nicht korrekt, wir *müssten* die überprüfung
            //  nachholen, sobald die seite online gestellt wird
            return;
        }
        $components = array();
        foreach (Vpc_Admin::getDependsOnRowInstances() as $a) {
            if ($a instanceof Vps_Component_Abstract_Admin_Interface_DependsOnRow) {
                $components = array_merge($components, $a->getComponentsDependingOnRow($row));
            }
        }

        $g = Vpc_Abstract::getSetting($this->_getParam('class'), 'generators');
        if (isset($g['detail']['dbIdShortcut'])) {
            //wenn auf sich selbst verlinkt ignorieren
            foreach ($components as $k=>&$c) {
                $c = $c->getPage();
                $news = Vps_Component_Data_Root::getInstance()
                    ->getComponentsByDbId($g['detail']['dbIdShortcut'].$row->id);
                foreach ($news as $n) {
                    if ($c->componentId == $n->getPage()->componentId) {
                        unset($components[$k]);
                    }
                }
            }
        }
        if ($components) {
            $msg = trlVps("You can not {0} this entry as it is used on the following pages:", $msgMethod);
            $msg .= Vps_Util_Component::getHtmlLocations($components);
            throw new Vps_ClientException($msg);
        }
    }
}
