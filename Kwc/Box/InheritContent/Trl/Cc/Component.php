<?php
class Kwc_Box_InheritContent_Trl_Cc_Component extends Kwc_Chained_Cc_Component
{
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = Kwc_Abstract::getTemplateVars($renderer);
        $ret['template'] = self::getTemplateFile($this->getData()->chained->chained->componentClass);
        $ret['child'] = $this->_getContentChild();
        return $ret;
    }

    private function _getContentChild()
    {
        $masterChild = $this->getData()->chained->chained->getComponent()->getContentChild();
        $c = Kwc_Chained_Cc_Component::getChainedByMaster($masterChild, $this->getData());
        $page = $this->getData();
        while($c && !$c->hasContent()) {
            while ($page && !$page->inherits) {
                $page = $page->parent;
                if ($page instanceof Kwf_Component_Data_Root) break;
            }
            if (!isset($page->chained)) {
                $c = null;
                break;
            }
            $masterChild = $page->chained->chained->getChildComponent('-'.$this->getData()->id)
                    ->getChildComponent(array('generator' => 'child'));
            $c = Kwc_Chained_Cc_Component::getChainedByMaster($masterChild, $this->getData());
            if ($page instanceof Kwf_Component_Data_Root) break;
            $page = $page->parent;
        }
        return $c;
    }
}
