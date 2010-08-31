<?php
class Vpc_Flash_Community_Component extends Vpc_Advanced_CommunityVideo_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = new Vpc_Advanced_CommunityVideo_Model(array(
            'proxyModel' => new Vps_Model_FnF(array(
                'columns' => array('component_id', 'data'),
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>'root_community', 'data' => 'a:4:{s:5:"width";s:3:"682";s:6:"height";s:3:"505";s:19:"show_similar_videos";s:1:"0";s:3:"url";s:42:"http://www.youtube.com/watch?v=0aRIlnQzw-A";}'),
                )
            ))
        ));
        return $ret;
    }
}
