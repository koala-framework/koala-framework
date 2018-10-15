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
        $ret = $this->_removeParts($ret);

        return $ret;
    }

    protected function _removeParts($ret)
    {
        $removeParts = $this->_getPartsToRemove();
        foreach ($removeParts as $removePart) {
            $ret = $this->_removePartFromOutput($ret, $removePart);
        }
        return $ret;
    }

    protected function _getPartsToRemove()
    {
        return Kwc_Abstract::getSetting($this->_data->componentClass, 'removeParts');
    }

    protected function _removePartFromOutput($output, $part)
    {
        $ret = $output;
        $kwfUp = Kwf_Config::getValue('application.uniquePrefix');
        if ($kwfUp) {
            $ret = preg_replace(
                "#<!-- $kwfUp\-$part -->.*?<!-- /$kwfUp\-$part -->#si",
                "<!-- $kwfUp-$part --><!-- removed --><!-- /$kwfUp-$part -->",
                $ret
            );
        }
        $ret = preg_replace(
            "#<!-- $part -->.*?<!-- /$part -->#si",
            "<!-- $part --><!-- removed --><!-- /$part -->",
            $ret
        );
        return $ret;
    }

}
