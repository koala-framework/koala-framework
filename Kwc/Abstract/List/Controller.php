<?php
class Kwc_Abstract_List_Controller extends Kwf_Controller_Action_Auto_Kwc_Grid
{
    protected $_buttons = array('save', 'add', 'delete');
    protected $_position = 'pos';

    protected $_showChildComponentGridColumns = true;

    protected function _initColumns()
    {
        parent::_initColumns();

        $this->_columns->add(new Kwf_Grid_Column_Visible());

        if ($this->_showChildComponentGridColumns) {
            $c = Kwc_Abstract::getChildComponentClass($this->_getParam('class'), 'child');
            foreach (Kwc_Admin::getInstance($c)->gridColumns() as $i) {
                $this->_columns->add($i);
            }
        }

        foreach (Kwf_Component_Data_Root::getInstance()->getPlugins('Kwf_Component_PluginRoot_Interface_GeneratorProperty') as $plugin) {
            $params = $plugin->getGeneratorProperty(Kwf_Component_Generator_Abstract::getInstance($this->_getParam('class'), 'child'));
            if ($params) {
                $editor = new Kwf_Form_Field_Select();
                $editor->setValues($params['values'])
                    ->setListWidth(200);
                $this->_columns->add(new Kwf_Grid_Column($params['name'],  $params['label']))
                    ->setEditor($editor)
                    ->setShowDataIndex($params['name'].'_text')
                    ->setData(new Kwf_Component_PluginRoot_GeneratorProperty_Data($plugin));
                $this->_columns->add(new Kwf_Grid_Column($params['name'].'_text'))
                    ->setData(new Kwf_Component_PluginRoot_GeneratorProperty_DataText($plugin));
            }
        }

    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row, $submitRow)
    {
        $row->visible = Kwc_Abstract::getSetting($this->_getParam('class'), 'defaultVisible');
    }

    public function jsonMultiUploadAction()
    {
        Zend_Registry::get('db')->beginTransaction();

        $asciiFilter = new Kwf_Filter_Ascii();
        $uploadIds = explode(',', $this->_getParam('uploadIds'));

        $max = Kwc_Abstract::getSetting($this->_getParam('class'), 'maxEntries');
        if ($max) {
            $s = new Kwf_Model_Select();
            $s->whereEquals('component_id', $this->_getParam('componentId'));
            if ($this->_model->countRows($s)+count($uploadIds) > $max) {
                throw new Kwf_Exception_Client(trlKwf("Can't create more than {0} entries.", $max));
            }
        }

        foreach ($uploadIds as $uploadId) {
            $uploadsModelClass = Kwf_Config::getValue('uploadsModelClass');
            $fileRow = Kwf_Model_Abstract::getInstance($uploadsModelClass)->getRow($uploadId);
            $row = $this->_model->createRow();
            $this->_beforeInsert($row, null);
            $this->_beforeSave($row, null);
            $row->save();
            $form = Kwc_Abstract_Form::createChildComponentForm($this->_getParam('class'), 'child');
            $form->setIdTemplate(null);
            $field = $this->_getFileUploadField($form);
            if (!$field) throw new Kwf_Exception("can't find file field");
            $form->setId($this->_getParam('componentId').'-'.$row->id);
            $postData = array(
                $field->getFieldName() => $uploadId
            );
            foreach ($this->_getAutoFillFilenameField($form) as $f) {
                if ($f->getAutoFillWithFilename() == 'filename') {
                    $postData[$f->getFieldName()] = $asciiFilter->filter($fileRow->filename);
                } else if ($f->getAutoFillWithFilename() == 'filenameWithExt') {
                    $postData[$f->getFieldName()] = $asciiFilter->filter($fileRow->filename).'.'.$fileRow->extension;
                }
            }
            $postData = array_merge($postData, $this->_getDefaultValues($form));
            $postData = $form->processInput(null, $postData);
            if ($errors = $form->validate(null, $postData)) {
                throw new Kwf_Exception('validate failed');
            }
            $form->prepareSave(null, $postData);
            $form->save(null, $postData);
        }

        Zend_Registry::get('db')->commit();
    }

    private function _getDefaultValues(Kwf_Form_Container_Abstract $form)
    {
        $ret = array();
        foreach ($form->getChildren() as $i) {
            if ($i instanceof Kwf_Form_Container_Abstract) {
                $ret = array_merge($ret, $this->_getDefaultValues($i));
            } else if ($i->getDefaultValue()) {
                $ret[$i->getFieldName()] = $i->getDefaultValue();
            }
        }
        return $ret;
    }

    private function _getFileUploadField($form)
    {
        foreach ($form as $i) {
            if ($i instanceof Kwf_Form_Field_File) {
                return $i;
            }
            $ret = $this->_getFileUploadField($i);
            if ($ret) return $ret;
        }
        return null;
    }

    private function _getAutoFillFilenameField($form)
    {
        $ret = array();
        foreach ($form as $i) {
            if ($i->getAutoFillWithFilename()) {
                $ret[] = $i;
            }
            if (!$i instanceof Kwf_Form_Field_MultiFields) {
                $ret = array_merge($ret, $this->_getAutoFillFilenameField($i));
            }
        }
        return $ret;
    }

    public function jsonDuplicateAction()
    {
        if (!isset($this->_permissions['duplicate']) || !$this->_permissions['duplicate']) {
            throw new Kwf_Exception("Duplicate is not allowed.");
        }

        $ids = $this->getRequest()->getParam($this->_primaryKey);
        $ids = explode(';', $ids);

        $this->_validateMaxEntries();

        $progressBar = null;

        $this->view->data = array('duplicatedIds' => array());
        ignore_user_abort(true);
        if (Zend_Registry::get('db')) Zend_Registry::get('db')->beginTransaction();
        $list = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->_getParam('componentId'), array('ignoreVisible'=>true, 'limit'=>1));
        foreach ($ids as $id) {
            $child = $list->getChildComponent(array('id'=>'-'.$id, 'ignoreVisible'=>true));
            $newChild = Kwf_Util_Component::duplicate($child, $list, $progressBar);
            $newChild->row->visible = false;
            $newChild->row->save();
            $this->view->data['duplicatedIds'][] = $newChild->id;
        }
        if (Zend_Registry::get('db')) Zend_Registry::get('db')->commit();
    }

    private function _validateMaxEntries()
    {
        $max = Kwc_Abstract::getSetting($this->_getParam('class'), 'maxEntries');
        if ($max) {
            $s = new Kwf_Model_Select();
            $s->whereEquals('component_id', $this->_getParam('componentId'));
            if ($this->_model->countRows($s)+1 > $max) {
                throw new Kwf_Exception_Client(trlKwf("Can't create more than {0} entries.", $max));
            }
        }
    }

    public function jsonInsertAction()
    {
        $this->_validateMaxEntries();
        parent::jsonInsertAction();
    }

    public function jsonCopyAction()
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($this->_getParam('componentId'), array('ignoreVisible'=>true));
        if (!$c) {
            throw new Kwf_Exception("Component not found");
        }
        $c = $c->getChildComponent(array('id' => $c->getGenerator('child')->getIdSeparator().$this->_getParam('id'), 'ignoreVisible'=>true));
        if (!$c) {
            throw new Kwf_Exception("Component not found");
        }
        $session = new Kwf_Session_Namespace('Kwc_Abstract_List:copy');
        $session->id = $c->dbId;
    }

    public function jsonPasteAction()
    {
        $this->_validateMaxEntries();

        $session = new Kwf_Session_Namespace('Kwc_Abstract_List:copy');
        $id = $session->id;
        if (!$id || !Kwf_Component_Data_Root::getInstance()->getComponentByDbId($id, array('ignoreVisible'=>true))) {
            throw new Kwf_Exception_Client(trlKwf('Clipboard is empty'));
        }
        $target = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($this->_getParam('componentId'), array('ignoreVisible'=>true));
        $source = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($id, array('ignoreVisible'=>true));

        if ($source->parent->componentClass != $target->componentClass) {
            throw new Kwf_Exception_Client(trlKwf('Source and target paragraphs are not compatible.'));
        }

        Kwf_Events_ModelObserver::getInstance()->disable(); //This would be slow as hell. But luckily we can be sure that for the new (duplicated) components there will be no view cache to clear.

        $progressBar = null;

        $newItem = Kwf_Util_Component::duplicate($source, $target, $progressBar);

        $row = $newItem->row;
        $target->getChildComponents(array('ignoreVisible'=>true));
        $row->pos = null; //moves to end of list
        $row->visible = false;
        $row->save();

        Kwf_Util_Component::afterDuplicate($source, $target);
        Kwf_Events_ModelObserver::getInstance()->enable();
    }
}
