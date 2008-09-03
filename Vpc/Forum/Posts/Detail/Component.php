<?php
class Vpc_Forum_Posts_Detail_Component extends Vpc_Posts_Detail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['edit']['component'] = 'Vpc_Posts_Detail_Edit_Component';
        $ret['generators']['quote']['component'] = 'Vpc_Forum_Posts_Detail_Quote_Component';
        return $ret;
    }
}
