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

    public function load($row, $postData = array())
    {
        if (isset($postData[$this->getFieldName()])) {
            $fileId = $postData[$this->getFieldName()];
        } else {
            $ref = $row->getRow()->getTable()->getReference('Vps_Dao_File', $this->getRuleKey());
            $fileId = $row->{$ref['columns'][0]};
        }
        $t = new Vps_Dao_File();
        $fileRow = $t->find($fileId)->current();
        if ($fileRow) {
            $return = $fileRow->getFileInfo();
        } else {
            $return = '';
        }
        return array_merge(parent::load($row, $postData),
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

        if ($this->getSave() !== false && $this->getInternalSave() !== false) {
            $data = $this->_getValueFromPostData($postData);
            //TODO: validierung von error-codes bei frontend-uploads
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

    public function processInput($postData)
    {
        $postData = parent::processInput($postData);

        if (isset($postData[$this->getFieldName().'_upload_id'])
            && !isset($postData[$this->getFieldName()])
        ) {
            $postData[$this->getFieldName()] = $postData[$this->getFieldName().'_upload_id'];
            unset($postData[$this->getFieldName().'_upload_id']);
        }

        if (isset($postData[$this->getFieldName()])
            && is_array($postData[$this->getFieldName()])
            && isset($postData[$this->getFieldName()]['tmp_name'])
        ) {
            //frontend formular, $_FILE werte
            $file = $postData[$this->getFieldName()];
            if ($file['error'] != UPLOAD_ERR_NO_FILE) {
                $t = new Vps_Dao_File();
                $fileRow = $t->createRow();
                $fileRow->uploadFile($file);
                $postData[$this->getFieldName()] = $fileRow->id;
            } else {
                unset($postData[$this->getFieldName()]);
            }
        }
        if (isset($postData[$this->getFieldName().'_del'])) {
            $postData[$this->getFieldName()] = '';
        }
        return $postData;
    }

    public function getTemplateVars($values, $namePostfix = '')
    {
        $name = $this->getFieldName();
        if (isset($values[$name])) {
            $value = $values[$name];
        } else {
            $value = $this->getDefaultValue();
        }
        $ret = parent::getTemplateVars($values);

        $name = htmlspecialchars($name);
        $ret['id'] = $name.$namePostfix;
        $ret['html'] = "<input type=\"file\" id=\"$ret[id]\" name=\"$name$namePostfix\" ".
                        " style=\"width: {$this->getWidth()}px\" />";
        if ($value) {
            $ret['html'] .= "<input type=\"hidden\" name=\"{$name}_upload_id{$namePostfix}\" ".
                        " value=\"$value[uploadId]\" />";
            $ret['html'] .= '<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class="delete" type="submit" name="'.$name.'_del'.$namePostfix.'" value="1">del</button>';
            $ret['html'] .= ''.$value['filename'].'.'.$value['extension'];
            $helper = new Vps_View_Helper_FileSize();
            $ret['html'] .= ' ('.$helper->fileSize($value['fileSize']).')';
            if ($value['image']) {
                //todo: with und height von image
                $ret['html'] .= " <img src=\"/vps/media/upload/preview?uploadId=$value[uploadId]\" alt=\"\"/>";
            }
        }
        return $ret;
    }
}
