<?php
class Vpc_Advanced_Amazon_Nodes_ProductsDirectory_Detail_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['product'] = $this->getData()->row;
        $ret['item'] = $ret['product']->getItem();
        $ret['similarProducts'] = array();
        if ($ret['item']->SimilarProducts) {
            foreach ($ret['item']->SimilarProducts as $p) {
                $p = $ret['product']->getModel()->getRow($p->ASIN);
                foreach ($p->getChildRows('ProductsToNodes') as $n) {
                    $s = new Vps_Component_Select();
                    $s->whereEquals('node_id', $n->node_id);
                    $s->whereGenerator('detail');
                    if ($this->getData()->parent->parent->countChildComponents($s)) {
                        $ret['similarProducts'][] = $this->getData()->parent->getChildComponent('_'.$p->asin);
                        break;
                    }
                }
            }
        }

        $ret['nodes'] = array();
        $nodes = $ret['product']->getChildRows('ProductsToNodes');
        foreach ($nodes as $node) {
            $s = new Vps_Component_Select();
            $s->whereEquals('node_id', $node->node_id);
            $s->whereGenerator('detail');
            $node = $this->getData()->parent->parent->getChildComponent($s);
            if ($node) {
                $ret['nodes'][] = $node;
            }
        }
        return $ret;
    }

    public function getViewCacheLifetime()
    {
        return 24*60*60;
    }

}
