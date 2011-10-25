<?php
class Kwc_Advanced_SocialBookmarks_TestComponent extends Kwc_Advanced_SocialBookmarks_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Advanced_SocialBookmarks_TestModel';
        return $ret;
    }
}
