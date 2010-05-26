<?php
class Vpc_Box_Tags_RelatedPages_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['cssClass'] = 'webStandard webListNone';
        $ret['placeholder']['headline'] = trlVpsStatic('More about this Topic');
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['related'] = array();
        $plugins = $this->getData()->getPage()->generator->getGeneratorPlugins();
        if (isset($plugins['tags'])) {
            $plugin = $plugins['tags'];
            $ret['related'] = $plugin->getComponentsWithSameTags($this->getData()->getPage());
        }
        return $ret;
    }

    public static function getStaticCacheVars($componentClass)
    {
        $ret = array();
        $ret[] = array(
            'model' => 'Vps_Component_Generator_Plugin_Tags_ComponentsToTagsModel'
        );
        return $ret;
    }
}
