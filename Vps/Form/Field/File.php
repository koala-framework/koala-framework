<?php
class Vps_Form_Field_File extends Vps_Form_Field_SimpleAbstract
{
    private $_fields;

    public function __construct($fieldname = null, $fieldLabel = null, $ruleKey = null)
    {
        parent::__construct($fieldname, $fieldLabel);
        $this->setAllowBlank(true); //standardwert für getAllowBlank
        $this->setAllowOnlyImages(false);
        $this->setRuleKey($ruleKey);
        $this->setXtype('swfuploadfield');
    }

    public function getMetaData()
    {
        $ret = parent::getMetaData();
        unset($ret['ruleKey']);
        $maxSize = ini_get('upload_max_filesize');
        if (strtolower(substr($maxSize, -1))=='k') {
            $maxSize = substr($maxSize, 0, -1)*1024;
        } else if (strtolower(substr($maxSize, -1))=='m') {
            $maxSize = substr($maxSize, 0, -1)*1024*1024;
        } else if (strtolower(substr($maxSize, -1))=='g') {
            $maxSize = substr($maxSize, 0, -1)*1024*1024*1024;
        }
        $ret['fileSizeLimit'] = $maxSize;
        return $ret;
    }

    public function load($row)
    {
        $fileRow = $row->getRow()->getFileRow($this->getRuleKey());
        if ($fileRow) {
            $return = $fileRow->getFileInfo();
        } else {
            $return = '';
        }
        return array_merge(parent::load($row),
            array($this->getFieldName() => $return));
    }

    protected function _getValueFromPostData($postData)
    {
        $ret = parent::_getValueFromPostData($postData);
        if ($ret == '' || $ret == 'null') $ret = null;
        return $ret;
    }
    public function validate($postData)
    {
        $ret = parent::validate($postData);

        if ($this->getSave() !== false) {
            $data = $this->_getValueFromPostData($postData);
            if ($data) {
                $t = new Vps_Dao_File();
                $row = $t->find($data)->current();
                if ($this->getAllowOnlyImages() && substr($row->mime_type, 0, 6) !=  'image/') {
                    $name = $this->getFieldLabel();
                    if (!$name) $name = $this->getName();
                    $ret[] = $name.': '.trlVps('This is not an image.');
                }
            }
        }
        return $ret;
    }
}
