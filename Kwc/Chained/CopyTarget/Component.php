<?php
abstract class Kwc_Chained_CopyTarget_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['target'] = array(
            'class' => 'Kwc_Chained_CopyTarget_TargetGenerator',
            'component' => null
        );
        $generator = Kwc_Abstract::getSetting('Kwc_Root_Category_Component', 'generators');
        if (isset($generator['page'])) {
            $ret['generators']['page'] = self::getPageGenerator($generator['page']);
        }
        return $ret;
    }

    public final static function getPageGenerator($generator)
    {
        $ret = Kwc_Chained_Cc_Component::createChainedGenerator($generator);
        $ret['class'] = 'Kwc_Chained_CopyTarget_PagesGenerator';
        return $ret;
    }

    public static function getPagesGeneratorComponentClass()
    {
        return 'Kwc_Root_Category_Component';
    }

    public abstract function getTargetComponent();
}
