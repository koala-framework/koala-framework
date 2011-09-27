<?php
class Vpc_Abstract_List_Controller extends Vps_Controller_Action_Auto_Vpc_Grid
{
    protected $_buttons = array('save', 'add', 'delete');
    protected $_position = 'pos';

    protected function _initColumns()
    {
        parent::_initColumns();
        $c = Vpc_Abstract::getChildComponentClass($this->_getParam('class'), 'child');
        foreach (Vpc_Admin::getInstance($c)->gridColumns() as $i) {
            $this->_columns->add($i);
        }
        $this->_columns->add(new Vps_Grid_Column_Visible());
    }

    protected function _beforeInsert($row)
    {
        if (is_null($row->visible)) $row->visible = 0;
    }

    public function jsonMultiUploadAction()
    {
        Zend_Registry::get('db')->beginTransaction();

        $asciiFilter = new Vps_Filter_Ascii();
        $uploadIds = explode(',', $this->_getParam('uploadIds'));
        foreach ($uploadIds as $uploadId) {
            $fileRow = Vps_Model_Abstract::getInstance('Vps_Uploads_Model')->getRow($uploadId);
            $row = $this->_model->createRow();
            $this->_beforeInsert($row);
            $this->_beforeSave($row);
            $row->save();
            $form = Vpc_Abstract_Form::createChildComponentForm($this->_getParam('class'), 'child');
            $form->setIdTemplate(null);
            $field = $this->_getFileUploadField($form);
            if (!$field) throw new Vps_Exception("can't find file field");
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
            $postData = $form->processInput(null, $postData);
            if ($errors = $form->validate(null, $postData)) {
                throw new Vps_Exception('validate failed');
            }
            $form->prepareSave(null, $postData);
            $form->save(null, $postData);
        }

        Zend_Registry::get('db')->commit();
    }


    private function _getFileUploadField($form)
    {
        foreach ($form as $i) {
            if ($i instanceof Vps_Form_Field_File) {
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
            if (!$i instanceof Vps_Form_Field_MultiFields) {
                $ret = array_merge($ret, $this->_getAutoFillFilenameField($i));
            }
        }
        return $ret;
    }
}
