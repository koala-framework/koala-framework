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
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Abstract/List/List.js';

        $ret['extConfig'] = 'Vpc_List_ChildPages_Teaser_ExtConfig';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['children'] = $this->getData()->getChildComponents(array('generator' => 'child'));
        return $ret;
    }

    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getStaticCacheMeta($componentClass);
        // Ist ziemlich grob, sonst müsste man sich eigenes Meta schreiben
        // Wenn eine Unterseite zB. offline genommen wird, muss der Cache gelöscht werden
        $ret[] = new Vps_Component_Cache_Meta_Static_Model('Vps_Component_Model');
        $ret[] = new Vps_Component_Cache_Meta_Static_Model('Vpc_Root_Category_GeneratorModel');
        return $ret;
    }
}
