<?php
class Kwc_Flash_Community_Component extends Kwc_Advanced_CommunityVideo_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = new Kwf_Model_FnF(array(
                'primaryKey' => 'component_id',
                'data'=> array(
                    array(
                        'component_id'=>'root_community',
                        "width"=>682,
                        "height"=>505,
                        "show_similar_videos"=>true,
                        "url"=>"http://www.youtube.com/watch?v=0aRIlnQzw-A",
                        "ratio"=>"16x9",
                        "size"=>"fullWidth",
                        "autoplay"=>true
                    ),
                )
            )
        );
        return $ret;
    }
}
