<?php
class Kwc_Basic_Image_Component extends Kwc_Abstract_Image_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Image');
        $ret['componentIcon'] = 'picture';
        $ret['emptyImage'] = false; // eg. 'Empty.jpg' in same folder

        $ret['apiContent'] = 'Kwc_Basic_Image_ApiContent';
        $ret['apiContentType'] = 'image';
        return $ret;
    }

    public static function validateSettings($settings, $componentClass)
    {
        parent::validateSettings($settings, $componentClass);
        if (isset($settings['useParentImage'])) {
            throw new Kwf_Exception("useParentImage doesn't exist anymore for Basic_Image, use Kwc_Basic_ImageParent_Component instead");
        }
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
            'mimeType' => $s['mime'],
            'dimensions' => array(
                'width' => $s[0],
                'height' => $s[1],
                'rotation' => 0,
            )
        );
    }
}
