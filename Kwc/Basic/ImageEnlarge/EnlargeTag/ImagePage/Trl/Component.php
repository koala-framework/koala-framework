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

        $c = $this->getData()->parent->getComponent();

        $size = $c->getImageDimension();
        $ret['width'] = $size['width'];
        $ret['height'] = $size['height'];
        $ret['imageUrl'] = $c->getImageUrl();
        $ret['options'] = (object)$c->getOptions();

        if (isset($ret['previous'])) {
            $ret['previous'] = self::getChainedByMaster($ret['previous'], $this->getData());
        }
        if (isset($ret['next'])) {
            $ret['next'] = self::getChainedByMaster($ret['next'], $this->getData());
        }
        return $ret;
    }
}