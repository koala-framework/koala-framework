<?php
class Kwc_Advanced_SocialBookmarks_Page1 extends Kwc_Basic_None_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page2'] = array(
            'component' => 'Kwc_Advanced_SocialBookmarks_Page2',
            'class' => 'Kwf_Component_Generator_Page_Static',
            'name' => 'page2'
        );
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        return $ret;
    }
}
