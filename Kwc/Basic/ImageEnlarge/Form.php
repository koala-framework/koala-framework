<?php
class Kwc_Basic_ImageEnlarge_Form extends Kwc_Abstract_Image_Form
{
    protected $_imageUploadFieldClass = 'Kwc_Basic_ImageEnlarge_ImageUploadField';

    protected function _initFields()
    {
        parent::_initFields();
        $this->setIdentifier('kwc-basic-imageenlarge-form');
        $linkTag = $this->getByName('linkTag');
        if ($linkTag) {
            $childs = $linkTag->getChildren();
            $childs = $childs[0]->getChildren();
            if ($childs[0] instanceof Kwf_Form_Container_Cards) {
                $linkTag->setTitle(trlKwf('Click on Preview Image').':');
            } else {
                $linkTag->setTitle(trlKwf('Image Enlarge').':');
            }
        }
    }

    private static function _findDimensionByChildComponentClassRecursive($class)
    {
        $parents = Kwc_Abstract::getSetting($class, 'parentClasses');
        if (in_array('Kwc_Basic_ImageEnlarge_EnlargeTag_Component', $parents)) {
            return Kwc_Abstract::getSetting($class, 'dimension');
        } else {
            foreach (Kwc_Abstract::getChildComponentClasses($class) as $childClass) {
                $dimension = self::_findDimensionByChildComponentClassRecursive($childClass);
                if ($dimension) {
                    return $dimension;
                }
            }
        }
        return false;
    }

    protected function _createImageUploadField($imageLabel)
    {
        $ret = parent::_createImageUploadField($imageLabel);
        $ret->setImageEnlargeDimension(self::_findDimensionByChildComponentClassRecursive($this->getClass()));
        return $ret;
    }
}
