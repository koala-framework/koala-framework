<?php
class Kwc_Advanced_IntegratorTemplate_Embed_ContentSender extends Kwf_Component_Abstract_ContentSender_Default
{
    protected function _render($includeMaster)
    {
        header('X-Robots-Tag: noindex');
        $domain = $this->_data->getParentByClass('Root_Domain_Component')->getAbsoluteUrl();
        $hasDynamicParts = false;
        $ret = parent::_render($includeMaster, $hasDynamicParts);
        $ret = str_replace('href="/', 'href="' . $domain . '/', $ret);
        $ret = str_replace('src="/', 'src="' . $domain . '/', $ret);
        $ret = str_replace("src='/", "src='" . $domain . '/', $ret);
        return $ret;
    }
}
