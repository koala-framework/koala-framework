<?php
class Kwc_Basic_ImageEnlarge_Form extends Kwc_Abstract_Image_Form
{
    protected function _initFields()
    {
        parent::_initFields();

        $linkTag = $this->getByName('linkTag');
        if ($linkTag) {
            $linkTag->setTitle(trlKwf('Click on Preview Image').':');
        }
    }
}
