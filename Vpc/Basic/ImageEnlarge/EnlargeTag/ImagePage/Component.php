<?php
class Vpc_Basic_ImageEnlarge_EnlargeTag_ImagePage_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['contentSender'] = 'Vpc_Basic_ImageEnlarge_EnlargeTag_ImagePage_ContentSender';
        $ret['assets']['dep'][] = 'VpsLightbox';
        $ret['cssClass'] = 'webStandard';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $c = $this->getData()->parent->getComponent();
        $size = $c->getImageDimensions();
        $ret['width'] = $size['width'];
        $ret['height'] = $size['height'];

        $ret['imageUrl'] = $c->getImageUrl();

        $ret['options'] = (object)$c->getOptions();


        $this->getData()->parent->parent;
        $enlargeTag = $this->getData()->parent;
        $imageEnlarge = $enlargeTag->parent;
        $parent = $imageEnlarge->parent;

        //TODO optimize in generator using something like whereNextSiblingOf / wherePreviousSiblingOf
        $allImages = $parent->getChildComponents(array('componentClass'=>$imageEnlarge->componentClass));

        $previous = null;
        foreach ($allImages as $c) {
            if ($c === $imageEnlarge) {
                $ret['previous'] = $previous;
            }
            if ($previous === $imageEnlarge) {
                $ret['next'] = $c;
                break;
            }
            $previous = $c;
        }

        return $ret;
    }
}
