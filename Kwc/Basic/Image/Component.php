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
        return $ret;
    }

    public static function validateSettings($settings, $componentClass)
    {
        parent::validateSettings($settings, $componentClass);
        if (isset($settings['useParentImage'])) {
            throw new Kwf_Exception("useParentImage doesn't exist anymore for Basic_Image, use Kwc_Basic_ImageParent_Component instead");
        }
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['imgCssClass'] = $this->_getSetting('imgCssClass');
        return $ret;
    }

    protected function _getEmptyImageData()
    {
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
