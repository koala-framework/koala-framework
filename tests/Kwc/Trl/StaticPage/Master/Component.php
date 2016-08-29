<?php
class Kwc_Trl_StaticPage_Master_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['foo'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Trl_StaticPage_Master_Foo_Component',
            'name' => trlKwfStatic('Visible')
        );
        $ret['flags']['hasHome'] = true;
        $ret['flags']['subroot'] = true;
        $ret['flags']['chainedType'] = 'Trl';
        $ret['flags']['hasBaseProperties'] = true;
        $ret['baseProperties'] = array('language');
        return $ret;
    }

    public function getBaseProperty($propertyName)
    {
        if ($propertyName == 'language') {
            return 'de';
        }
        return null;
    }
}
