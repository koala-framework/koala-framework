<?php
class Kwc_Basic_LinkParent_Model extends Kwc_Basic_Link_Model
{
    protected function _init()
    {
        parent::_init();
        $this->setDefault(array('text' => trlKwf('« back')));
    }
}
