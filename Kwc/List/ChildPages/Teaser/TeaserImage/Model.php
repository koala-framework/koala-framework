<?php
class Kwc_List_ChildPages_Teaser_TeaserImage_Model extends Kwf_Component_FieldModel
{
    protected function _init()
    {
        parent::_init();
        $this->setDefault(array('link_text' => '&raquo;'));
    }
}
