<?php
class Vpc_Advanced_Amazon_Nodes_Admin extends Vpc_Admin
{
    public function getExtConfig()
    {
        return array_merge(parent::getExtConfig(), array(
            'xtype'=>'vps.autogrid'
        ));
    }
}
