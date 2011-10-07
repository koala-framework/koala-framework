<?php
class Vpc_Advanced_SocialBookmarks_Page1 extends Vpc_Basic_Empty_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page2'] = array(
            'component' => 'Vpc_Advanced_SocialBookmarks_Page2',
            'class' => 'Vps_Component_Generator_Page_Static',
            'name' => 'page2'
        );
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        return $ret;
    }
}
