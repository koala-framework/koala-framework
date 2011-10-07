<?php
class Kwc_Advanced_Amazon_Nodes_TestComponent extends Kwc_Advanced_Amazon_Nodes_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Advanced_Amazon_Nodes_TestModel';
        $ret['generators']['detail']['model'] = 'Kwc_Advanced_Amazon_Nodes_TestNodesModel';
        return $ret;
    }
}
