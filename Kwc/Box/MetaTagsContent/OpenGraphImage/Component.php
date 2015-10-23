<?php
class Kwc_Box_MetaTagsContent_OpenGraphImage_Component extends Kwc_Abstract_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Open Graph').' '.trlKwfStatic('Image');
        $ret['imageLabel'] = trlKwfStatic('Open Graph').' '.trlKwfStatic('Image');
        $ret['dimensions'] = array(
            'default'=>array(
                'text' => trlKwfStatic('default'),
                'width' => 1800,
                'height' => 1200,
                'cover' => false,
            ),
        );
        $ret['altText'] = false;
        $ret['editFilename'] = false;
        $ret['titleText'] = false;
        $ret['imageCaption'] = false;
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $protocol = 'http';
        $domain = $this->getData()->getDomain();
        $imageUrl = $this->getImageUrl();
        $ret['imageUrl'] = '';
        if ($imageUrl) {
            $ret['imageUrl'] = "$protocol://$domain$imageUrl";
        }
        return $ret;
    }
}
