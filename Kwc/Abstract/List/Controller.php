<?php
class Kwc_Abstract_List_Controller extends Kwf_Controller_Action_Auto_Kwc_Grid
{
    protected $_buttons = array('save', 'add', 'delete');
    protected $_position = 'pos';

    protected function _initColumns()
    {
        parent::_initColumns();
        $c = Kwc_Abstract::getChildComponentClass($this->_getParam('class'), 'child');
        foreach (Kwc_Admin::getInstance($c)->gridColumns() as $i) {
            $this->_columns->add($i);
        }
        $this->_columns->add(new Kwf_Grid_Column_Visible());
    }

    protected function _beforeInsert($row)
    {
        if (is_null($row->visible)) $row->visible = 0;
    }

    public function jsonMultiUploadAction()
    {
        Zend_Registry::get('db')->beginTransaction();

        $asciiFilter = new Kwf_Filter_Ascii();
        $uploadIds = explode(',', $this->_getParam('uploadIds'));

        $max = Kwc_Abstract::getSetting($this->_getParam('class'), 'maxEntries');
        $s = new Kwf_Model_Select();
        $s->whereEquals('component_id', $this->_getParam('componentId'));
        if ($this->_model->countRows($s)+count($uploadIds) >= $max) {
            throw new Kwf_Exception_Client(trlKwf("Can't create more than {0} entries.", $max));
        }

        foreach ($uploadIds as $uploadId) {
            $fileRow = Kwf_Model_Abstract::getInstance('Kwf_Uploads_Model')->getRow($uploadId);
            $row = $this->_model->createRow();
            $this->_beforeInsert($row);
            $this->_beforeSave($row);
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

        $s = new Kwf_Model_Select();
        $s->whereEquals('component_id', $this->_getParam('componentId'));
        $max = Kwc_Abstract::getSetting($this->_getParam('class'), 'maxEntries');
        if ($this->_model->countRows($s)+count($ids) >= $max) {
            throw new Kwf_Exception_Client(trlKwf("Can't create more than {0} entries.", $max));
        }

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

    public function jsonInsertAction()
    {
        $max = Kwc_Abstract::getSetting($this->_getParam('class'), 'maxEntries');
        $s = new Kwf_Model_Select();
        $s->whereEquals('component_id', $this->_getParam('componentId'));
        if ($this->_model->countRows($s)+1 >= $max) {
            throw new Kwf_Exception_Client(trlKwf("Can't create more than {0} entries.", $max));
        }

        parent::jsonInsertAction();
    }
}
