<?php
class Vpc_Box_InheritContent_Cc_Component extends Vpc_Chained_Cc_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['child'] = $this->_getContentChild();
        return $ret;
    }

    private function _getContentChild()
    {
        $masterChild = $this->getData()->chained->getComponent()->getContentChild();
        $c = Vpc_Chained_Cc_Component::getChainedByMaster($masterChild, $this->getData());
        $page = $this->getData();
        while($c && !$c->hasContent()) {
            while ($page && !$page->inherits) {
                $page = $page->parent;
                if ($page instanceof Vps_Component_Data_Root) break;
            }
            if (!isset($page->chained)) {
                $c = null;
                break;
            }
            $masterChild = $page->chained->getChildComponent('-'.$this->getData()->id)
                    ->getChildComponent(array('generator' => 'child'));
            $c = Vpc_Chained_Cc_Component::getChainedByMaster($masterChild, $this->getData());
            if ($page instanceof Vps_Component_Data_Root) break;
            $page = $page->parent;
        }
        return $c;
    }
}
