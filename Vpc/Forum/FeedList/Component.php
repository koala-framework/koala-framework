<?php
class Vpc_Forum_FeedList_Component extends Vpc_Forum_AllPostsList_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['feed'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Forum_FeedList_Feed_Component',
            'name' => trlVps('Feed')
        );
        return $ret;
    }

    public function getSelect()
    {
        $ret = parent::getSelect();
        $ret->order('create_time', 'DESC');
        return $ret;
    }
}
