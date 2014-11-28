<?php
class Kwc_Cards_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['cards1'] = array(
            'component' => 'Kwc_Cards_Composite_Component',
            'class' => 'Kwf_Component_Generator_Page_Static',
            'name' => 'Cards1'
        );
        unset($ret['generators']['page']);
        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
