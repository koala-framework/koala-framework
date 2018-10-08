<?php
class Kwc_Advanced_IntegratorTemplate_Embed_ContentSender extends Kwf_Component_Abstract_ContentSender_Default
{
    protected function _render($includeMaster, &$hasDynamicParts)
    {
        header('X-Robots-Tag: noindex');
        $domain = 'http'.(isset($_SERVER['HTTPS']) ? 's' : '').'://'.$_SERVER['HTTP_HOST'];
        $ret = parent::_render($includeMaster, $hasDynamicParts);
        $ret = preg_replace('#(href|src|action)=(["\'])(/[^/])#', '$1=$2' . $domain . '$3', $ret);
        $ret = preg_replace('#<!-- postRenderPlugin.*?-->#s', '', $ret);
        return $ret;
    }
}
