<?php
class Kwc_Basic_Image_Component extends Kwc_Abstract_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Image');
        $ret['componentIcon'] = new Kwf_Asset('picture');
        $ret['imgCssClass'] = '';
        $ret['emptyImage'] = false; // eg. 'Empty.jpg' in same folder
        $ret['useParentImage'] = false;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['imgCssClass'] = $this->_getSetting('imgCssClass');
        return $ret;
    }
    protected function _getAltText()
    {
        if ($this->_getSetting('useParentImage')) {
            return $this->getData()->parent->getComponent()->_getAltText();
        } else {
            return parent::_getAltText();
        }
    }

    public function getImageData()
    {
        if ($this->_getSetting('useParentImage')) {
            return $this->getData()->parent->getComponent()->getImageData();
        } else {
            return parent::getImageData();
        }
    }

    protected function _getEmptyImageData()
    {
        if (!$this->_getSetting('emptyImage') && $this->_getSetting('useParentImage')) {
            return $this->getData()->parent->getComponent()->_getEmptyImageData();
        } else {
            $emptyImage = $this->_getSetting('emptyImage');
            if (!$emptyImage) return null;
            $ext = substr($emptyImage, strrpos($emptyImage, '.') + 1);
            $filename = substr($emptyImage, 0, strrpos($emptyImage, '.'));
            $file = Kwc_Admin::getComponentFile($this, $filename, $ext);
            $s = getimagesize($file);
            return array(
                'filename' => $emptyImage,
                'file' => $file,
                'mimeType' => $s['mime']
            );
        }
    }
}
