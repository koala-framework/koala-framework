<?php
class Kwc_Advanced_SocialBookmarks_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page1'] = array(
            'component' => 'Kwc_Advanced_SocialBookmarks_Page1',
            'class' => 'Kwf_Component_Generator_Page_Static',
            'name' => 'page1'
        );
        $ret['generators']['socialBookmarks'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'Kwc_Advanced_SocialBookmarks_TestComponent',
            'inherit' => true,
            'priority' => 0
        );
        unset($ret['generators']['page']);
        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}