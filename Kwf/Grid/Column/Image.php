<?php
class Kwf_Grid_Column_Image extends Kwf_Grid_Column
    implements Kwf_Media_Output_Interface
{
    public function __construct($dataIndex = null, $header = null, $ruleKey = null)
    {
        parent::__construct($dataIndex, $header, 60);
        $this->setType('string');
        $this->setRenderer('image');
        $this->setRuleKey($ruleKey);
        $this->setSortable(false);
    }

    public function load($row, $role, $info)
    {
        $ret = null;
        $uploadRow = $row->getParentRow($this->getRuleKey());
        if ($uploadRow) {
            $ret = Kwf_Media::getUrl(get_class($this), $uploadRow->id, 'preview', $uploadRow);
        }
        return $ret;
    }

    public static function getMediaOutput($uploadId, $type, $className)
    {
        $uploadRow = Kwf_Model_Abstract::getInstance('Kwf_Uploads_Model')->getRow($uploadId);
        if ($type == 'preview') {
            $size = array(
                'width' => 50,
                'height' => 19,
                'scale' => Kwf_Media_Image::SCALE_BESTFIT
            );
        } else {
            throw new Kwf_Exception_NotFound();
        }
        return array(
            'contents' => Kwf_Media_Image::scale($uploadRow->getFileSource(), $size),
            'mimeType' => $uploadRow->mime_type
        );
    }
}
