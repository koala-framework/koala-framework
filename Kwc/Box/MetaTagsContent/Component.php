<?php
class Kwc_Box_MetaTagsContent_Component extends Kwc_Box_MetaTags_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Meta Tags');
        $ret['ownModel'] = 'Kwc_Box_MetaTagsContent_Model';
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_None';
        $ret['generators']['child']['component']['ogImage'] = 'Kwc_Box_MetaTagsContent_OpenGraphImage_Component';
        $ret['flags']['hasPageMeta'] = true;
        return $ret;
    }

    public function getPageMeta()
    {
        $row = $this->getRow();
        return array(
            'noindex' => (bool)$row->noindex,
            'sitemap_changefreq' => $row->sitemap_changefreq,
            'sitemap_priority' => (float)$row->sitemap_priority
        );
    }

    protected function _getMetaTags()
    {
        $ret = parent::_getMetaTags();
        $row = $this->_getRow();
        if ($row->description) $ret['description'] = $row->description;
        if ($row->og_title) $ret['og:title'] = $row->og_title;
        if ($row->og_description) $ret['og:description'] = $row->og_description;
        if ($row->noindex) {
            if (isset($ret['robots']) && strpos($ret['robots'], 'noindex') === false) {
                $ret['robots'] .= ',noindex';
            } else {
                $ret['robots'] = 'noindex';
            }
        }
        $ret['og:url'] = $this->getData()->getPage()->getAbsoluteUrl();

        $c = $this->getData()->parent;
        while ($c) {
            if (($c->inherits && Kwc_Abstract::getFlag($c->componentClass, 'subroot')) || $c->componentId == 'root') {
                $metaTags = $c->getChildComponent(array('id'=>'-'.$this->getData()->id, 'componentClass'=>$this->getData()->componentClass));
                if ($metaTags && is_instance_of($metaTags->componentClass, 'Kwc_Box_MetaTagsContent_Component')) {
                    $row = $metaTags->getComponent()->getRow();
                    if (!isset($ret['og:title']) && $row->og_title) {
                        $ret['og:title'] = $row->og_title;
                    }
                    if (!isset($ret['og:site_name']) && $row->og_site_name) {
                        $ret['og:site_name'] = $row->og_site_name;
                    }
                }
            }
            $c = $c->parent;
        }
        return $ret;
    }
}
