<?php
class Kwc_List_ChildPages_Teaser_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['child'] = array(
            'class' => 'Kwc_List_ChildPages_Teaser_Generator',
            'component' => 'Kwc_List_ChildPages_Teaser_TeaserImage_Component'
        );
        $ret['childModel'] = 'Kwc_List_ChildPages_Teaser_Model';

        $ret['componentName'] = trlKwf('List child pages');
        $ret['cssClass'] = 'webStandard';
        $ret['assetsAdmin']['dep'][] = 'KwfProxyPanel';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Abstract/List/List.js';

        $ret['extConfig'] = 'Kwc_List_ChildPages_Teaser_ExtConfig';
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
        $ret[] = new Kwf_Component_Cache_Meta_Static_Model('Kwf_Component_Model');
        $ret[] = new Kwf_Component_Cache_Meta_Static_Model('Kwc_Root_Category_GeneratorModel');
        return $ret;
    }
}
