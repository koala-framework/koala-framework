<?php
class Vpc_Advanced_SocialBookmarks_TestComponent extends Vpc_Advanced_SocialBookmarks_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Advanced_SocialBookmarks_TestModel';
        return $ret;
    }
}
