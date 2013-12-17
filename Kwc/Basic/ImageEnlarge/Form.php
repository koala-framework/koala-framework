<?php
class Kwc_Basic_ImageEnlarge_Form extends Kwc_Abstract_Image_Form
{
    protected function _initFields()
    {
        parent::_initFields();

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
}
