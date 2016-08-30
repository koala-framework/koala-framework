<?php
class Kwc_Advanced_SocialBookmarks_Trl_Component extends Kwc_Chained_Trl_MasterAsChild_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['editComponents'][] = 'child'; //kann das vielleicht im parent gemacht werden?
        $ret['flags']['hasAlternativeComponent'] = true;
        return $ret;
    }


    public static function getAlternativeComponents()
    {
        return array(
            'inherit'=>'Kwc_Advanced_SocialBookmarks_Inherit_Trl_Component.Kwc_Advanced_SocialBookmarks_Inherit_Component'
        );
    }

    public static function useAlternativeComponent($componentClass, $parentData, $generator)
    {
        return false;
    }
}