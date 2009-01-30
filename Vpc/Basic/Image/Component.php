<?php
class Vpc_Basic_Image_Component extends Vpc_Abstract_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Image');
        $ret['componentIcon'] = new Vps_Asset('picture');
        $ret['pdfMaxWidth'] = 0;
        $ret['pdfMaxDpi'] = 150;
        $ret['imgCssClass'] = '';
        $ret['emptyImage'] = false;
        $ret['useParentImage'] = false;
        return $ret;
    }


    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['imgCssClass'] = $this->_getSetting('imgCssClass');
        return $ret;
    }

    public function hasContent()
    {
        if ($this->_getSetting('useParentImage')) {
            return $this->getData()->parent->hasContent();
        }
        if ($this->_getSetting('emptyImage')) return true;
        return parent::hasContent();
    }


    public function getImageUrl()
    {
        $ret = parent::getImageUrl();
        if (!$ret && $file = self::_getEmptyImage(get_class($this))) {
            $filename = $this->_getSetting('emptyImage');
            $id = $this->getData()->dbId;
            $ret = Vps_Media::getUrl(get_class($this), $id, 'default', $filename);
        }
        return $ret;
    }

    public function getImageRow()
    {
        if ($this->_getSetting('useParentImage')) {
            return $this->getModel()->getRow($this->getData()->parent->dbId);
        } else {
            return parent::getImageRow();
        }
    }

    protected static function _getEmptyImage($className)
    {
        $emptyImage = Vpc_Abstract::getSetting($className, 'emptyImage');
        if (!$emptyImage) return null;
        $ext = substr($emptyImage, strrpos($emptyImage, '.') + 1);
        $filename = substr($emptyImage, 0, strrpos($emptyImage, '.'));
        return Vpc_Admin::getComponentFile($className, $filename, $ext);
    }

}
