<?php
class Vpc_Basic_LinkParent_Model extends Vpc_Basic_Link_Model
{
    protected function _init()
    {
        parent::_init();
        $this->setDefault(array('text' => trlVps('« back')));
    }
}
