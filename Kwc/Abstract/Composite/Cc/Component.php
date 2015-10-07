<?php
class Kwc_Abstract_Composite_Cc_Component extends Kwc_Chained_Cc_Component
{
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        foreach ($this->getData()->getChildComponents(array('generator' => 'child')) as $c) {
            if (isset($ret[$c->id]) && $ret[$c->id]) $ret[$c->id] = $c; // Bei TextImage kann zB. Bild ausgeblendet werden und soll dann in Übersetzung auch nicht angezeigt werden
        }
        return $ret;
    }

    public function hasContent()
    {
        foreach ($this->getData()->getChildComponents(array('generator' => 'child')) as $c) {
            if ($c->hasContent()) return true;
        }
        return false;
    }
}
