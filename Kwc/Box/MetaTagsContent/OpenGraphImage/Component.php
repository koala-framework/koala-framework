<?php
class Kwc_Box_MetaTagsContent_OpenGraphImage_Component extends Kwc_Abstract_Image_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
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
        $ret['flags']['hasFulltext'] = false;
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);

        $ret['imageUrl'] = $this->getAbsoluteImageUrl();
        $ret['width'] = '';
        $ret['height'] = '';
        $imageDimensions = $this->getImageDimensions();
        if ($imageDimensions) {
            $ret['width'] = $imageDimensions['width'];
            $ret['height'] = $imageDimensions['height'];
        }
        return $ret;
    }
}
