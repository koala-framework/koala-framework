<?php
class Vpc_ListChildPages_Teaser_TeaserImage_Model extends Vps_Component_FieldModel
{
    protected function _init()
    {
        $this->setDefault(array('link_text' => trlVps('Read more »')));
        parent::_init();
    }
}
