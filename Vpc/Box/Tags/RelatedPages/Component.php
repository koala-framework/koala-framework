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
        $ret['related'] = $this->_getRelatedPages();
        return $ret;
    }

    protected function _getRelatedPages()
    {
        $ret = array();
        if (!$this->getData()->getPage() || !$this->getData()->getPage()->generator) return $ret;
        $plugin = $this->getData()->getPage()->generator->getGeneratorPlugin('tags');
        if ($plugin) {
            $ret = $plugin->getComponentsWithSameTags($this->getData()->getPage());
            foreach ($ret as $k=>$i) {
                if ($i->getPage()->componentId == $this->getData()->getPage()->componentId) {
                    unset($ret[$k]);
                }
            }
        }
        return $ret;
    }

    public function hasContent()
    {
        return !!$this->_getRelatedPages();
    }

    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getStaticCacheMeta($componentClass);
        $ret[] = new Vps_Component_Cache_Meta_Static_Model('Vps_Component_Generator_Plugin_Tags_ComponentsToTagsModel');
        return $ret;
    }
}
