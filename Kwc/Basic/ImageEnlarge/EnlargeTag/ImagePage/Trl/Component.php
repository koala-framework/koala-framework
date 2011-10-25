<?php
class Kwc_Basic_ImageEnlarge_EnlargeTag_ImagePage_Trl_Component extends Kwc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['contentSender'] = 'Kwc_Basic_ImageEnlarge_EnlargeTag_ImagePage_Trl_ContentSender';
        return $ret;
    }
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $c = $this->getData()->parent->getChildComponent('-image')->getComponent();
        $size = $c->getImageDimensions();
        $ret['width'] = $size['width'];
        $ret['height'] = $size['height'];

        $ret['imageUrl'] = $c->getImageUrl();

        return $ret;
    }
}