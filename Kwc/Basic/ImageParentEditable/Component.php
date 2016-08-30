<?php
class Kwc_Basic_ImageParentEditable_Component extends Kwc_Abstract_Image_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Basic/ImageParentEditable/ImageUploadField.js';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Basic/ImageParentEditable/ImageFile.js';
        return $ret;
    }

    public function getImageData()
    {
        $ret = parent::getImageData();
        if (!$ret) {
            $ret = $this->getData()->parent->getComponent()->getImageData();
        }
        return $ret;
    }
}
