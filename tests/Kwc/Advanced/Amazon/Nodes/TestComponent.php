<?php
class Vpc_Advanced_Amazon_Nodes_TestComponent extends Vpc_Advanced_Amazon_Nodes_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Advanced_Amazon_Nodes_TestModel';
        $ret['generators']['detail']['model'] = 'Vpc_Advanced_Amazon_Nodes_TestNodesModel';
        return $ret;
    }
}
