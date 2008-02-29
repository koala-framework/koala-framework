<?php
class Vpc_Forum_Admin extends Vpc_Admin
{
    public function getExtConfig()
    {
        return array_merge(parent::getExtConfig(), array(
            'xtype'=>'vps.autotree'
        ));
    }
}