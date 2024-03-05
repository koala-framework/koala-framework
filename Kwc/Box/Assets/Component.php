<?php
class Kwc_Box_Assets_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['flags']['hasHeaderIncludeCode'] = true;
        $ret['flags']['hasInjectIntoRenderedHtml'] = true;
        return $ret;
    }

    public function getIncludeCode()
    {
        return $this->getData();
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['language'] = $this->getData()->getLanguage();
        $ret['subroot'] = $this->getData()->getSubroot();
        $ret['assetsPackages'] = array('Frontend');
        $ret['kwfUp'] = Kwf_Config::getValue('application.uniquePrefix') ? Kwf_Config::getValue('application.uniquePrefix').'-' : '';
        return $ret;
    }

    /**
     * @deprecated
     */
    protected final function _getSection()
    {
    }

    public function injectIntoRenderedHtml($html)
    {
        $kwfUp = Kwf_Config::getValue('application.uniquePrefix') ? Kwf_Config::getValue('application.uniquePrefix').'-' : null;
        $startPos = strpos($html, '<!-- '.$kwfUp.'assets -->');
        $endPos = strpos($html, '<!-- /'.$kwfUp.'assets -->')+16+strlen($kwfUp);
        $html = substr($html, 0, $startPos)
                .$this->getData()->render()
                .substr($html, $endPos);
        return $html;
    }
}
