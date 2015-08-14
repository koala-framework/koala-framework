<?php
class Kwc_Basic_ImageEnlarge_EnlargeTag_ImagePage_Data extends Kwf_Component_Data
{
    public function __get($var)
    {
        if ($var == 'url') {
            return $this->parent->getComponent()->getImageUrl();
        } else if ($var == 'lightbox_url') {
            return parent::__get('url');
        } else {
            return parent::__get($var);
        }
    }
}
