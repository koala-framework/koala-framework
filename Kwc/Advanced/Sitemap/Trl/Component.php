<?php
class Kwc_Advanced_Sitemap_Trl_Component extends Kwc_Chained_Trl_Component
{
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['target'] = self::getChainedByMaster($ret['target'], $this->getData());
        $ret['listHtml'] = '';
        if ($ret['target']) {
            $ret['listHtml'] = Kwc_Advanced_Sitemap_Component::getListHtml($renderer, $ret['target'], 0, $ret['levels'], $this->getData()->componentClass);
        }
        return $ret;
    }
}

