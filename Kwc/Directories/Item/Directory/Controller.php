<?php
class Kwc_Directories_Item_Directory_Controller extends Kwf_Controller_Action_Auto_Kwc_Grid
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
        if (is_instance_of(Kwc_Abstract::getSetting($this->_getParam('class'), 'extConfig'), 'Kwc_Directories_Item_Directory_ExtConfigEditButtons')
            || is_instance_of(Kwc_Abstract::getSetting($this->_getParam('class'), 'extConfigControllerIndex'), 'Kwc_Directories_Item_Directory_ExtConfigEditButtons')) {
            $url = Kwc_Admin::getInstance($this->_getParam('class'))->getControllerUrl('Form');
            $editDialog = array(
                'autoForm' => 'Kwc.Directories.Item.Directory.EditFormPanel',
                'controllerUrl' => $url
            );
            if (!empty($this->_editDialog['width'])) $editDialog['width'] = $this->_editDialog['width'];
            if (!empty($this->_editDialog['height'])) $editDialog['height'] = $this->_editDialog['height'];
            $this->_editDialog = $editDialog;
        }

        if ($this->_columns[0] instanceof Kwf_Grid_Column_Button) {
            throw new Kwf_Exception("Override Controller and add at least one column (button must not be first)");
        }
    }

    protected function _initColumns()
    {
        $extConfigType = false;
        if (is_instance_of(Kwc_Abstract::getSetting($this->_getParam('class'), 'extConfig'), 'Kwc_Directories_Item_Directory_ExtConfigEditButtons')) {
            $extConfigType = 'extConfig';
        } else if (is_instance_of(Kwc_Abstract::getSetting($this->_getParam('class'), 'extConfigControllerIndex'), 'Kwc_Directories_Item_Directory_ExtConfigEditButtons')) {
            $extConfigType = 'extConfigControllerIndex';
        }
        if ($extConfigType) {
            //shows editDialog
            if ($this->_editDialog) {
                $this->_columns->add(new Kwf_Grid_Column_Button('properties', ' ', 20))
                    ->setButtonIcon('/assets/silkicons/newspaper.png')
                    ->setTooltip(trlKwf('Properties'));
            }

            $extConfig = Kwf_Component_Abstract_ExtConfig_Abstract::getInstance($this->_getParam('class'), $extConfigType)
                        ->getConfig(Kwf_Component_Abstract_ExtConfig_Abstract::TYPE_DEFAULT);

            $extConfig = $extConfig['items'];
                    if ($extConfig['countDetailClasses'] > 1 && !$this->_getModel()->hasColumn('component')) {
                throw new Kwf_Exception('If you have more than one detail-component your table has to have a column named "component"');
            }
            if ($extConfig['countDetailClasses'] == 1 && $this->_getModel()->hasColumn('component')) {
                throw new Kwf_Exception('If you have just one detail-component your table is not allowed to have a column named "component"');
            }
            $i=0;
            foreach ($extConfig['contentEditComponents'] as $ec) {
                $name = Kwf_Trl::getInstance()->trlStaticExecute(Kwc_Abstract::getSetting($ec['componentClass'], 'componentName'));
                $icon = Kwc_Abstract::getSetting($ec['componentClass'], 'componentIcon');
                $this->_columns->add(new Kwc_Directories_Item_Directory_ControllerEditButton('edit_'.$i, ' ', 20))
                    ->setColumnType('editContent')
                    ->setEditComponentClass($ec['componentClass'])
                    ->setEditComponent($ec['component'])
                    ->setEditType($ec['type'])
                    ->setEditIdTemplate($ec['idTemplate'])
                    ->setEditComponentIdSuffix($ec['componentIdSuffix'])
                    ->setButtonIcon($icon->toString(array('arrow')))
                    ->setTooltip(trlKwf('Edit {0}', $name));
                $i++;
            }
        }
        $this->_columns->add(new Kwf_Grid_Column('component_class'))
            ->setData(new Kwf_Data_Kwc_ComponentClass($this->_getParam('class'), 'detail'));
        $this->_columns->add(new Kwf_Grid_Column('edit_components'))
            ->setData(new Kwf_Data_Kwc_EditComponents($this->_getParam('class'), 'detail'));
    }

    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        parent::_beforeSave($row);
        if ($row->getModel()->hasColumn('visible') && !$row->visible) {
            $this->_checkRowIndependence($row, trlKwf('hide'));
        }
    }

    protected function _beforeDelete(Kwf_Model_Row_Interface $row)
    {
        parent::_beforeDelete($row);
        $this->_checkRowIndependence($row, trlKwf('delete'));
    }

    private function _checkRowIndependence(Kwf_Model_Row_Interface $row, $msgMethod)
    {
        $c = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->_getParam('componentId'));
        // wenn zB Newsletter statisch in root erstellt wurde, gibts kein visible
        if (!$c) {
            //wenn seite offline ist ignorieren
            //  ist nicht natürlich nicht korrekt, wir *müssten* die überprüfung
            //  nachholen, sobald die seite online gestellt wird
            return;
        }
        $components = array();
        foreach (Kwc_Admin::getDependsOnRowInstances() as $a) {
            if ($a instanceof Kwf_Component_Abstract_Admin_Interface_DependsOnRow) {
                $components = array_merge($components, $a->getComponentsDependingOnRow($row));
            }
        }

        $g = Kwc_Abstract::getSetting($this->_getParam('class'), 'generators');
        if (isset($g['detail']['dbIdShortcut'])) {
            //wenn auf sich selbst verlinkt ignorieren
            foreach ($components as $k=>&$c) {
                $c = $c->getPage();
                $news = Kwf_Component_Data_Root::getInstance()
                    ->getComponentsByDbId($g['detail']['dbIdShortcut'].$row->id);
                foreach ($news as $n) {
                    if ($c->componentId == $n->getPage()->componentId) {
                        unset($components[$k]);
                    }
                }
            }
        }
        if ($components) {
            $msg = trlKwf("You can not {0} this entry as it is used on the following pages:", $msgMethod);
            $msg .= Kwf_Util_Component::getHtmlLocations($components);
            throw new Kwf_ClientException($msg);
        }
    }
}
