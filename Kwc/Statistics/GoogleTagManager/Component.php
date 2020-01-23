<?php
class Kwc_Statistics_GoogleTagManager_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['assets']['dep'][] = 'KwfStatistics';
        $ret['flags']['hasHeaderIncludeCode'] = true;
        $ret['flags']['hasFooterIncludeCode'] = true;
        return $ret;
    }

    public function getIncludeCode($position)
    {
        if ($position == 'header') {
            return $this->getData();
        } else if ($position == 'footer') {
            $containerId = $this->getData()->getBaseProperty('statistics.googleTagManager.containerId');
            return '<!-- Google Tag Manager (noscript) --><noscript>'.
                '<iframe src="https://www.googletagmanager.com/ns.html?id=GTM-'.$containerId.'" '.
                'height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>'.
                '<!-- End Google Tag Manager (noscript) -->';
        }
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['containerId'] = $this->getData()->getBaseProperty('statistics.googleTagManager.containerId');
        $ret['queryParams'] = $this->getData()->getBaseProperty('statistics.googleTagManager.queryParams');
        return $ret;
    }
}
