<?php
class Kwc_Box_Tags_RelatedPages_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['rootElementClass'] = 'kwfUp-webStandard kwfUp-webListNone';
        $ret['placeholder']['headline'] = trlKwfStatic('More about this Topic');
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
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
}
