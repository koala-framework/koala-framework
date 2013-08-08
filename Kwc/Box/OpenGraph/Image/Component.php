<?php
class Kwc_Box_OpenGraph_Image_Component extends Kwc_Abstract_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Image');
        $ret['dimensions'] = array(
            'default'=>array(
                'text' => trlKwf('default'),
                'width' => 200,
                'height' => 200,
                'bestfit' => false,
            ),
        );
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
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
