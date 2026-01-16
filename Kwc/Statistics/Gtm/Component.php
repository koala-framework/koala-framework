<?php
class Kwc_Statistics_Gtm_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['flags']['hasHeaderIncludeCode'] = true;
        $ret['flags']['hasFooterIncludeCode'] = true;
        return $ret;
    }

    public function getDataLayer()
    {
        $dataLayer = array();

        $component = $this->getData();
        while ($component) {
            if (Kwc_Abstract::getFlag($component->componentClass, 'getDataLayer')) {
                $ret = array_merge($ret, $component->getComponent()->getDataLayer());
            }
            $component = $component->parent;
        }

        return $dataLayer;
    }

    public function getIncludeCode($position)
    {
        if ($position == 'header') {
            return $this->getData();
        } else if ($position == 'footer') {
            $vars = $this->_getGtmVars();
            $ret = "<!-- GoogleTagManager -->";
            if ($vars['ignore']) {
                $ret .= "<!-- ignored -->";
            } else {
                $ret .= "<noscript><iframe src=\"https://www.googletagmanager.com/ns.html?id=GTM-{$vars['containerId']}{$vars['queryParams']}\" height=\"0\" width=\"0\" style=\"display:none;visibility:hidden\"></iframe></noscript>";
            }
            $ret .= "<!-- /GoogleTagManager -->";
            return $ret;
        }
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret = array_merge($ret, $this->_getGtmVars());
        return $ret;
    }

    protected function _getGtmVars()
    {
        $ret = array();
        $ret['containerId'] = $this->getData()->getBaseProperty('statistics.googleTagManager.containerId');
        $ret['queryParams'] = $this->getData()->getBaseProperty('statistics.googleTagManager.queryParams');
        $ret['dataLayer'] = $this->getDataLayer();
        if ($ret['dataLayer']) $ret['dataLayer'] = array($ret['dataLayer']);
        $ret['ignore'] =
            $this->getData()->getBaseProperty('statistics.ignore') ||
            $this->getData()->getBaseProperty('statistics.googleTagManager.ignore');
        return $ret;
    }
}
