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
        $pageGenerator = Kwc_Chained_Cc_Component::createChainedGenerator('Kwc_Root_Category_Component', 'page');
        if ($pageGenerator) {
            $ret['generators']['page'] = $pageGenerator;
            $ret['generators']['page']['class'] = 'Kwc_Chained_CopyTarget_PagesGenerator';
        }
        return $ret;
    }

    public abstract function getTargetComponent();
}
