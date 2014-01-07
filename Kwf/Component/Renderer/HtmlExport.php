<?php
class Kwf_Component_Renderer_HtmlExport extends Kwf_Component_Renderer_Abstract
{
    protected function _getRendererName()
    {
        return 'export_html';
    }

    public function getTemplate(Kwf_Component_Data $component, $type)
    {
        if ($type == 'Component') {
            $tpl = 'Component.exp';
        } else if ($type == 'Partial') {
            $tpl = 'Partial.exp';
        }
        $template = Kwc_Abstract::getTemplateFile($component->componentClass, $tpl);
        if (!$template) {
            $template = parent::getTemplate($component, $type);
        }
        return $template;
    }

    public function renderComponent($component)
    {
        $ret = parent::renderComponent($component);
        $p = new Kwf_Component_Renderer_HtmlExport_UrlParser('http://'.$component->getDomain());
        $ret = $p->parse($ret);
        return $ret;
    }
}
