<?php
class Vpc_Abstract_Composite_Cc_Component extends Vpc_Chained_Cc_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        foreach ($this->getData()->getChildComponents(array('generator' => 'child')) as $c) {
            if ($ret[$c->id]) $ret[$c->id] = $c; // Bei TextImage kann zB. Bild ausgeblendet werden und soll dann in Übersetzung auch nicht angezeigt werden
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
