<?php
class Kwc_Box_MetaTags_Trl_Component extends Kwc_Abstract_Composite_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['flags']['hasHeaderIncludeCode'] = true;
        $ret['flags']['hasInjectIntoRenderedHtml'] = true;
        return $ret;
    }

    public function injectIntoRenderedHtml($html)
    {
        return Kwc_Box_MetaTags_Component::injectMeta($html, $this->getData()->render());
    }

    public function getIncludeCode()
    {
        return $this->getData();
    }

    protected function _getMetaTags()
    {
        return Kwc_Box_MetaTags_Component::getMetaTagsForData($this->getData());
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['metaTags'] = $this->_getMetaTags();
        $ret['canonicalUrl'] = $this->getData()->getAbsoluteUrl();
        return $ret;
    }
}
