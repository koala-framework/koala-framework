<?php
/**
 * @package Form
 */
class Kwf_Form_Field_ImageViewer extends Kwf_Form_Field_Abstract
    implements Kwf_Media_Output_Interface
{
    public function __construct($field_name = null, $field_label = null, $ruleKey = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('imageviewer');
        $this->setRuleKey($ruleKey);
    }

    public function setRuleKey($ruleKey)
    {
        return $this->setProperty('ruleKey', $ruleKey);
    }

    public function load($row)
    {
        $data = array(
            'imageUrl' => false,
            'previewUrl' => false,
        );
        $uploadRow = $row->getParentRow($this->getRuleKey());
        if ($uploadRow) {
            $data['imageUrl'] = Kwf_Media::getUrl(get_class($this), $uploadRow->id, 'original', $uploadRow);
            $data['previewUrl'] = Kwf_Media::getUrl(get_class($this), $uploadRow->id, 'preview', $uploadRow);
        }
        return array($this->getFieldName() => (object)$data);
    }

    public static function getMediaOutput($uploadId, $type, $className)
    {
        $uploadsModelClass = Kwf_Config::getValue('uploadsModelClass');
        $uploadRow = Kwf_Model_Abstract::getInstance($uploadsModelClass)->getRow($uploadId);
        if ($type == 'original') {
            return array(
                'file' => $uploadRow->getFileSource(),
                'mimeType' => $uploadRow->mime_type
            );
        } else if ($type == 'preview') {
            $size = array(
                'width' => 150,
                'height' => 200,
                'cover' => false,
            );
            return array(
                'contents' => Kwf_Media_Image::scale($uploadRow, $size),
                'mimeType' => $uploadRow->mime_type
            );
        } else {
            throw new Kwf_Exception_NotFound();
        }
    }
}
