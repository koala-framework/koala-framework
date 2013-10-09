<?php
/**
 * @package Form
 */
class Kwf_Form_Field_File extends Kwf_Form_Field_SimpleAbstract
{
    private $_fields;

    public function __construct($fieldname = null, $fieldLabel = null)
    {
        parent::__construct($fieldname, $fieldLabel);
        $this->setFrontendButtonText(trlKwfStatic('Browse').'...');
        $this->setAllowBlank(true); //standardwert für getAllowBlank
        $this->setAllowOnlyImages(false);
        $this->setMaxResolution(false);
        $this->setXtype('kwf.file');
        $maxSize = ini_get('upload_max_filesize');
        if (strtolower(substr($maxSize, -1))=='k') {
            $maxSize = substr($maxSize, 0, -1)*1024;
        } else if (strtolower(substr($maxSize, -1))=='m') {
            $maxSize = substr($maxSize, 0, -1)*1024*1024;
        } else if (strtolower(substr($maxSize, -1))=='g') {
            $maxSize = substr($maxSize, 0, -1)*1024*1024*1024;
        }
        $this->setFileSizeLimit($maxSize.' B');
        $this->setEmptyMessage(trlKwfStatic("Please choose a file"));
    }

    protected function _getTrlProperties()
    {
        $ret = parent::_getTrlProperties();
        $ret[] = 'frontendButtonText';
        return $ret;
    }

    public function getMetaData($model)
    {
        $ret = parent::getMetaData($model);
        unset($ret['ruleKey']);
        return $ret;
    }

    public function load($row, $postData = array())
    {
        if ($this->getSave() === false || !$row) {
            return array();
        }

        if (array_key_exists($this->getFieldName(), $postData)) {
            $fileId = $postData[$this->getFieldName()];
            if ($fileId) {
                $fileRow = $row->getModel()
                                ->getReferencedModel($this->getName())
                                ->getRow($fileId);
            } else {
                $fileRow = null;
            }
        } else {
            $fileRow = $row->getParentRow($this->getName());
        }
        if ($fileRow) {
            $return = $fileRow->getFileInfo();
        } else {
            $return = '';
        }
        return array($this->getFieldName() => $return);
    }

    protected function _getValueFromPostData($postData)
    {
        $ret = parent::_getValueFromPostData($postData);
        if ($ret == '' || $ret == 'null') $ret = null;
        return $ret;
    }

    public function validate($row, $postData)
    {
        $ret = parent::validate($row, $postData);

        if ($this->getSave() !== false) {
            $data = $this->_getValueFromPostData($postData);
            if ($data) {
                $fileModel = $row->getModel()->getReferencedModel($this->getName());
                $row = $fileModel->getRow($data);
                if ($this->getAllowOnlyImages() && substr($row->mime_type, 0, 6) !=  'image/') {
                    $ret[] = array(
                        'message' => trlKwf('This is not an image.'),
                        'field' => $this
                    );
                }
            }
        }
        return $ret;
    }

    public function processInput($row, $postData)
    {
        $postData = parent::processInput($row, $postData);

        if ($this->getSave() === false) return $postData;

        if (isset($postData[$this->getFieldName().'_upload_id'])
            && (!isset($postData[$this->getFieldName()])
                || $postData[$this->getFieldName()]['error'] == UPLOAD_ERR_NO_FILE)
        ) {
            if (!$postData[$this->getFieldName().'_upload_id']) {
                $postData[$this->getFieldName()] = null;
            } else {
                $splited = explode('_', $postData[$this->getFieldName().'_upload_id']);
                if (count($splited) != 2) {
                    throw new Kwf_Exception('Id doesn\'t consist of all needed parts.');
                }
                $uploadsRow = $row->getModel()
                    ->getReferencedModel($this->getName())
                    ->getRow($splited[0]);
                if ($uploadsRow->getHashKey() != $splited[1]) {
                    throw new Kwf_Exception('Posted hashKey does not match file-hashkey.');
                }
                $postData[$this->getFieldName()] = (int)$postData[$this->getFieldName().'_upload_id'];
            }
            unset($postData[$this->getFieldName().'_upload_id']);
        }

        if (isset($postData[$this->getFieldName()])
            && is_array($postData[$this->getFieldName()])
            && isset($postData[$this->getFieldName()]['tmp_name'])
        ) {
            //frontend formular, $_FILE werte
            $file = $postData[$this->getFieldName()];
            unset($postData[$this->getFieldName()]);
            if ($file['error'] != UPLOAD_ERR_NO_FILE) {
                $fileModel = $row->getModel()->getReferencedModel($this->getName());
                $fileRow = $fileModel->createRow();
                $fileRow->uploadFile($file);
                $postData[$this->getFieldName()] = $fileRow->id;
                if (isset($postData[$this->getFieldName().'_upload_id'])) {
                    unset($postData[$this->getFieldName().'_upload_id']);
                }
            }
        }
        if (isset($postData[$this->getFieldName().'_del'])) {
            unset($postData[$this->getFieldName().'_del']);
            $postData[$this->getFieldName()] = null;
        }

        if (isset($postData[$this->getFieldName()])
            && $postData[$this->getFieldName()] === ''
        ) {
            $postData[$this->getFieldName()] = null;
        }

        return $postData;
    }

    public function prepareSave(Kwf_Model_Row_Interface $row, $postData)
    {
        if ($this->getSave() === false) return;

        $ref = $row->getModel()->getReference($this->getName());
        if (array_key_exists($this->getFieldName(), $postData)) {
            $row->{$ref['column']} = $postData[$this->getFieldName()];
        }
    }

    public function getTemplateVars($values, $namePostfix = '', $idPrefix = '')
    {
        $name = $this->getFieldName();
        $value = isset($values[$name]) ? $values[$name] : null;
        $ret = parent::getTemplateVars($values, $namePostfix, $idPrefix);

        $name = htmlspecialchars($name);
        $ret['id'] = $idPrefix.str_replace(array('[', ']'), array('_', '_'), $name.$namePostfix);
        $ret['html']  = "<div class=\"kwfFormFieldFileInnerImg\">\n";
        if ($value) {
            $ret['html'] .= "<input type=\"hidden\" name=\"{$name}_upload_id{$namePostfix}\" ".
                        " value=\"$value[uploadId]_$value[hashKey]\" />";
            if ($value['image']) {
                //todo: width und height von image
                $ret['html'] .= " <img src=\"/kwf/media/upload/preview?uploadId=$value[uploadId]&hashKey=$value[hashKey]&amp;size=frontend\" alt=\"\" width=\"100\" height=\"100\" />";
            }
        }
        $ret['html'] .= '</div>';
        $ret['html'] .= "<div class=\"kwfFormFieldFileInnerContent\">\n";
        $ret['html'] .= "<div class=\"imagePath kwfFormFieldFileUploadWrapper\">\n";
            $ret['html'] .= "<input class=\"fileSelector\" type=\"file\" id=\"$ret[id]\" name=\"$name$namePostfix\" ".
                            " style=\"width: {$this->getWidth()}px\" onchange=\"document.getElementById(this.id+'_underlayText').value = this.value;\" />";
            $ret['html'] .= '<div class="underlayFileSelector">';
            $ret['html'] .= '<input type="text" id="'.$ret['id'].'_underlayText" style="width: '.$this->getWidth().'px;" />';
            $ret['html'] .= ' <a href="#" class="kwfFormFieldFileUploadButton" onclick="return false;">'.$this->getFrontendButtonText().'</a>';
            $ret['html'] .= '</div>';
        $ret['html'] .= '</div>';
        if ($value) {
            $ret['html'] .= "<div class=\"imageTitle\">\n";
            $ret['html'] .= ''.$value['filename'].'.'.$value['extension'];
            $helper = new Kwf_View_Helper_FileSize();
            $ret['html'] .= ' ('.$helper->fileSize($value['fileSize']).')';
            $ret['html'] .= '</div>';
            $ret['html'] .= '<div class="deleteImage"><button class="deleteImage" type="submit" name="'.$name.'_del'.$namePostfix.'" value="1">'.trlKwf("Delete").'</button></div>';
            $uploadId = $value['uploadId'];
            $hashKey = '_'.$value['hashKey'];
        } else {
            $uploadId = '0';
            $hashKey = '';
        }
        $ret['html'] .= "<input type=\"hidden\" name=\"{$name}_upload_id{$namePostfix}\" ".
                    " value=\"$uploadId$hashKey\" />";
        $ret['html'] .= '</div>';
        return $ret;
    }
}
