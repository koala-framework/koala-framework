<?php
class Kwc_Basic_ImageEnlarge_EnlargeTag_ImagePage_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['contentSender'] = 'Kwc_Basic_ImageEnlarge_EnlargeTag_ImagePage_ContentSender';
        $ret['assetsDefer']['dep'][] = 'KwfLightbox';
        $ret['rootElementClass'] = 'kwfUp-webStandard';
        $ret['flags']['noIndex'] = true;
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);

        $c = $this->getData()->parent->getComponent();

        $size = $c->getImageDimensions();
        $ret['width'] = $size['width'];
        $ret['height'] = $size['height'];
        $ret['imageUrl'] = $c->getImageUrl();
        $ret['options'] = (object)$c->getOptions();

        // Next-Previous Links
        $imageEnlarge = $this->getData()->parent->parent;
        if (is_instance_of($imageEnlarge->componentClass, 'Kwc_Basic_ImageEnlarge_Component') ||
            is_instance_of($imageEnlarge->componentClass, 'Kwc_Basic_ImageEnlargeParent_Component')) {
            // Only show links when it's an ImageEnlarge (no LinkTag)
            $parent = $imageEnlarge->parent;
            $getChildren = array();
            if (is_instance_of($parent->componentClass, 'Kwc_Abstract_List_Component')) {
                //it's in an List_Gallery
            } else if ($parent->parent &&
                is_instance_of($parent->parent->componentClass, 'Kwc_Abstract_List_Component')
            ) {
                //it's in an List_Switch with ImageEnlarge as large component (we have to go up one more level)
                $getChildren = array('-'.$imageEnlarge->id);
                $imageEnlarge = $imageEnlarge->parent;
            }
            $links = self::getPreviousAndNextImagePage(
                $this->getData()->componentClass, $imageEnlarge, $getChildren
            );
            $ret = array_merge($ret, $links);
        } else {
            $ret['next'] = null;
            $ret['previous'] = null;
        }

        $imageData = $c->getImageData();
        $ret = array_merge($ret,
            Kwf_Media_Output_Component::getResponsiveImageVars($size, $imageData['file'])
        );

        $ret['baseUrl'] = $c->getBaseImageUrl();
        return $ret;
    }

    public static function getPreviousAndNextImagePage($componentClass, $imageEnlarge, $getChildren, $ignoreOwnVisible = false)
    {
        $ret = array();
        $parent = $imageEnlarge->parent;
        //TODO optimize in generator using something like whereNextSiblingOf / wherePreviousSiblingOf
        $allImages = $imageEnlarge->parent->getChildComponents(
            array('componentClass'=>$imageEnlarge->componentClass, 'ignoreVisible'=>$ignoreOwnVisible)
        );
        $previous = null;
        $previousWasImageEnlarge = false;
        foreach ($allImages as $c) {
            $cVisible = !isset($c->row) || !isset($c->row->visible) || $c->row->visible;
            if ($c === $imageEnlarge) {
                $ret['previous'] = self::_getImagePage($previous, $getChildren);
                $previousWasImageEnlarge = true;
            } else if ($previousWasImageEnlarge && $cVisible) {
                $ret['next'] = self::_getImagePage($c, $getChildren);
                break;
            }
            if ($cVisible) { $previous = $c; }
        }
        return $ret;
    }

    private static function _getImagePage($data, $getChildren)
    {
        if (!$data) return null;
        $ret = $data;
        foreach ($getChildren as $c) {
            $ret = $ret->getChildComponent($c);
        }
        if (!$ret) return null;
        $ret = $ret->getChildComponent('-linkTag');
        if (is_instance_of($ret->componentClass, 'Kwc_Basic_LinkTag_Component')) {
            $ret = $ret->getChildComponent('-child');
        }
        $ret = $ret->getChildComponent('_imagePage');
        return $ret;
    }
}
