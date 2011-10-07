<?php
class Vpc_List_ChildPages_Teaser_TeaserImage_Model extends Vps_Component_FieldModel
{
    protected function _init()
    {
        parent::_init();
        $this->setDefault(array('link_text' => trlVps('Read more »')));
    }
}
