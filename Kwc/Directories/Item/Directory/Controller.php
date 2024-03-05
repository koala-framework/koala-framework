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
        if ($this->_columns[0] instanceof Kwf_Grid_Column && $this->_columns[0]->getName() == 'component_class') {
            throw new Kwf_Exception("Override Controller and add at least one column (component_class must not be first)");
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
                $icon = new Kwf_Asset($icon);
                $data = new Kwc_Directories_Item_Directory_ControllerEditButtonData();
                $data->setEditComponent($ec['component']);
                $this->_columns->add(new Kwf_Grid_Column_Button('edit_'.$i, ' ', 20))
                    ->setData($data)
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
        if ($this->_model->hasColumn('component')) {
            $this->_columns->add(new Kwf_Grid_Column('component'));
        }
        $this->_columns->add(new Kwf_Grid_Column('component_class'))
            ->setData(new Kwf_Data_Kwc_ComponentClass($this->_getParam('class'), 'detail'));
        $this->_columns->add(new Kwf_Grid_Column('edit_components'))
            ->setData(new Kwf_Data_Kwc_EditComponents($this->_getParam('class'), 'detail'));
    }

    protected function _beforeSave(Kwf_Model_Row_Interface $row, $submitRow)
    {
        parent::_beforeSave($row, $submitRow);
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

    public function jsonDuplicateAction()
    {
        if (!isset($this->_permissions['duplicate']) || !$this->_permissions['duplicate']) {
            throw new Kwf_Exception("Duplicate is not allowed.");
        }

        $ids = $this->getRequest()->getParam($this->_primaryKey);
        $ids = explode(';', $ids);

        $progressBar = null;

        $this->view->data = array('duplicatedIds' => array());
        ignore_user_abort(true);
        if (Zend_Registry::get('db')) Zend_Registry::get('db')->beginTransaction();
        $dir = Kwf_Component_Data_Root::getInstance()->getComponentByDbId(
            $this->_getParam('componentId'),
            array('ignoreVisible'=>true, 'limit'=>1)
        );
        foreach ($ids as $id) {
            $sep = $dir->getGenerator('detail')->getIdSeparator();
            $child = $dir->getChildComponent(array('id'=>$sep.$id, 'ignoreVisible'=>true));
            $newChild = Kwf_Util_Component::duplicate($child, $dir, $progressBar);
            $newChild->row->save();
            $this->view->data['duplicatedIds'][] = $newChild->id;
        }
        if (Zend_Registry::get('db')) Zend_Registry::get('db')->commit();
    }

    public function jsonMultiUploadAction()
    {
        $componentId = $this->_getParam('componentId');
        $component = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($componentId, array('ignoreVisible' => true, 'limit' => 1));
        if (Kwc_Abstract::getSetting($component->componentClass, 'multiFileUpload')) {
            $uploadIds = $this->_getParam('uploadIds');
            $uploadIds = explode(',', $uploadIds);
            foreach ($uploadIds as $uploadId) {
                $this->_createNewDetailComponentFromUpload($uploadId, $component);
            }
        }
    }

    protected function _createNewDetailComponentFromUpload($uploadId, $component)
    {
        throw new Kwf_Exception_NotYetImplemented('You have to override _createNewDetailComponentFormUpload function to create child components on multiFileUpload');
    }
}
