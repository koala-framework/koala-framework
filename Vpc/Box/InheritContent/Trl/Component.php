<?php
class Vpc_Box_InheritContent_Trl_Component extends Vpc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['viewCache'] = false;
        $ret['editComponents'] = array('child');
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $masterChild = $this->getData()->chained->getComponent()->getContentChild();
        $c = Vpc_Chained_Trl_Component::getChainedByMaster($masterChild, $this->getData());

        $page = $this->getData();
        while(!$c->hasContent()) {
            while ($page && !$page->inherits) {
                $page = $page->parent;
                if ($page instanceof Vps_Component_Data_Root) break;
            }
            if (!isset($page->chained)) break;
            $masterChild = $page->chained->getChildComponent('-'.$this->getData()->id)
                    ->getChildComponent(array('generator' => 'child'));
            $c = Vpc_Chained_Trl_Component::getChainedByMaster($masterChild, $this->getData());
            if ($page instanceof Vps_Component_Data_Root) break;
            $page = $page->parent;
        }
        $ret['child'] = $c;
        return $ret;
    }

    public static function getNextContentChild($page, $inheritContentChildId)
    {
        while ($page && !$page->inherits) {
            $page = $page->parent;
            if ($page instanceof Vps_Component_Data_Root) break;
        }
        return $page->getChildComponent('-'.$inheritContentChildId)
                ->getChildComponent(array('generator' => 'child'));
    }
}
