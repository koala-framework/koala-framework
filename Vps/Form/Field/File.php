<?php
class Vps_Form_Field_File extends Vps_Form_Field_Abstract
{
    private $_fields;

    public function __construct($fieldname = null, $title = null, $ruleKey = null)
    {
        parent::__construct($fieldname);
        $this->setFileFieldLabel($title);
        $this->setLayout('form');
        $this->setBorder(false);
        $this->setBaseCls('x-plain');
        $this->setAllowBlank(true); //standardwert für getAllowBlank
        $this->getAllowOnlyImages(false);
        $this->setRuleKey($ruleKey);
    }

    protected function _getFields()
    {
        if (!isset($this->_fields)) {
            $this->_fields = new Vps_Collection_FormFields();
            $title = $this->getFileFieldLabel();
            if (!$title) $title = 'Upload new File';
            $this->_fields->add(new Vps_Form_Field_TextField($this->getFieldName()))
                ->setFieldLabel($title)
                ->setXtype('fileuploadfield');
            if ($this->getAllowBlank()) {
                $this->_fields->add(new Vps_Form_Field_Checkbox($this->getFieldName() . '_delete', ''))
                    ->setBoxLabel(trlVps('Delete'))
                    ->setHideLabel(true)
                    ->setXtype('filecheckbox');
            }
        }
        return $this->_fields;
    }

    public function getMetaData()
    {
        $ret = parent::getMetaData();
        unset($ret['allowOnlyImages']);
        unset($ret['ruleKey']);
        $ret['items'] = $this->_getFields()->getMetaData();
        return $ret;
    }

    public function load($row)
    {
        $url = $row->getRow()->getFileUrl($this->getRuleKey(), 'original');
        $return = array(
            'url' => $url,
            'uploaded' => !is_null($url)
        );
        return array($this->getFieldName() . '_delete' => $return);
    }

    public function prepareSave(Vps_Model_Db_Row $row, $postData)
    {
        parent::prepareSave($row, $postData);
        $fieldName = $this->getFieldName();
        $name = $this->getName();

        $file = isset($_FILES[$fieldName]) ? $_FILES[$fieldName] : array();

        $uploadRow = $row->findParentRow('Vps_Dao_File', $this->getRuleKey());

        if (!$uploadRow && (!isset($file['error']) || $file['error'] == UPLOAD_ERR_NO_FILE)) {
            if ($this->getAllowBlank() == false) {
                throw new Vps_ClientException('Please select a file.');
            } else {
                return;
            }
        }

        if (isset($file['tmp_name']) && is_file($file['tmp_name'])) {

            if ($this->getAllowOnlyImages() && substr($file['type'], 0, 6) != 'image/') {
                throw new Vps_ClientException(trlVps('File-Type not allowed. Only Images are allowed.'));
            }

            try {
                if (!$uploadRow) {
                    $t = new Vps_Dao_File();
                    $uploadRow = $t->createRow();
                }
                $uploadRow->uploadFile($file);
                $row->$name = $uploadRow->id;

            } catch (Vps_Exception $e) {
                throw new Vps_ClientException($e->getMessage());
            }
        }
        if ($uploadRow && $this->getAllowBlank()
                    && isset($postData[$fieldName . '_delete'])
                    && $postData[$fieldName . '_delete'] == '1') {
            $row->$name = null;
        }
    }
    public function save(Vps_Model_Db_Row $row, $postData)
    {
        parent::save($row, $postData);
        $uploadRow = $row->findParentRow('Vps_Dao_File', $this->getRuleKey());
        $name = $this->getName();
        if ($uploadRow && is_null($row->$name)) {
            $uploadRow->delete();
        }
    }
}
