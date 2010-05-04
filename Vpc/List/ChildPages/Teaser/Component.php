<?php
class Vpc_List_ChildPages_Teaser_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['child'] = array(
            'class' => 'Vpc_List_ChildPages_Teaser_Generator',
            'component' => 'Vpc_List_ChildPages_Teaser_TeaserImage_Component'
        );
        $ret['childModel'] = 'Vpc_List_ChildPages_Teaser_Model';

        $ret['componentName'] = trlVps('List child pages');
        $ret['cssClass'] = 'webStandard';
        $ret['assetsAdmin']['dep'][] = 'VpsProxyPanel';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Abstract/List/Panel.js';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['children'] = $this->getData()->getChildComponents(array('generator' => 'child'));
        return $ret;
    }

    public static function getStaticCacheVars()
    {
        $ret = array();
        $ret[] = array(
            'model' => 'Vps_Component_Model'
        );
        $ret[] = array(
            'model' => 'Vpc_Root_Category_GeneratorModel'
        );
        return $ret;
    }
}
