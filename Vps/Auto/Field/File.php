<?php
class Vps_Auto_Field_File extends Vps_Auto_Field_Abstract
{
    private $_fields;

    public function __construct($fieldname = null, $title = null)
    {
        parent::__construct($fieldname);
        $this->setFileFieldLabel($title);
        $this->setLayout('form');
        $this->setBorder(false);
        $this->setBaseCls('x-plain');
        $this->setAllowBlank(true); //standardwert für getAllowBlank
    }

    protected function _getFields()
    {
        if (!isset($this->_fields)) {
            $this->_fields = new Vps_Collection();
            $title = $this->getFileFieldLabel();
            if (!$title) $title = 'Upload new File';
            $this->_fields->add(new Vps_Auto_Field_TextField($this->getFieldName(), 'Upload new File'))
                ->setFieldLabel($title)
                ->setInputType('file');
            if ($this->getAllowBlank()) {
                $this->_fields->add(new Vps_Auto_Field_Checkbox($this->getFieldName() . '_delete', ''))
                    ->setBoxLabel('Delete')
                    ->setXType('filecheckbox');
            }
        }
        return $this->_fields;
    }

    public function getMetaData()
    {
        $ret = parent::getMetaData();
        $ret['items'] = $this->_getFields()->getMetaData();
        return $ret;
    }

    public function load($row)
    {
        $table = new Vps_Dao_File();
        $name = $this->getName();
        $return = array();
        $return['url'] = $table->getOriginalUrl($row->$name);
        $return['uploaded'] = !is_null($return['url']);
        return array($this->getFieldName() . '_delete' => $return);
    }

    public function prepareSave(Zend_Db_Table_Row_Abstract $row, $postData)
    {
        parent::prepareSave($row, $postData);
        $fieldName = $this->getFieldName();
        $name = $this->getName();

        $file = isset($_FILES[$fieldName]) ? $_FILES[$fieldName] : array();
        $fileTable = new Vps_Dao_File();
        $fileTable->deleteCache($row->$name);
        
        if ($row->$name == 0 && (!isset($file['error']) || $file['error'] == UPLOAD_ERR_NO_FILE)) {
            if (!is_null($this->getAllowBlank()) && $this->getAllowBlank() == false) {
                throw new Vps_ClientException('Please select a file');
            } else {
                return;
            }
        }
        
        if (isset($file['tmp_name']) && is_file($file['tmp_name'])) {
            $extension = substr(strrchr($file['name'], '.'), 1);
            if (!in_array($extension, $this->getExtensions())) {
                throw new Vps_ClientException('File-extension not allowed. Allowed: ' . implode(', ', $this->getExtensions()));
            }
            
            try {
                $id = $fileTable->uploadFile($file, $row->$name);
                if ($id) {
                    $row->$name = $id;
                }
            } catch (Vps_Exception $e) {
                throw new Vps_ClientException($e->getMessage());
            }
        } else if ($this->getAllowBlank()
                    && isset($postData[$fieldName . '_delete'])
                    && $postData[$fieldName . '_delete'] == '1') {
            $row->$name = null;
            $fileTable->delete($row->$name);
        }

        $fileTable->deleteCache($row->$name);
    }

}
