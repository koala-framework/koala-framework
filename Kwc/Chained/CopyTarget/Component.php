<?php
abstract class Kwc_Chained_CopyTarget_Component extends Kwc_Abstract
{
    public static function getSettings($includePageGenerator = 'Kwc_Root_Category_Component')
    {
        $ret = parent::getSettings();
        $ret['generators']['target'] = array(
            'class' => 'Kwc_Chained_CopyTarget_TargetGenerator',
            'component' => null
        );

        if ($includePageGenerator) {
            $pageGenerator = Kwc_Chained_Cc_Component::createChainedGenerator($includePageGenerator, 'page');
            if ($pageGenerator) {
                $ret['generators']['page'] = $pageGenerator;
                $ret['generators']['page']['class'] = 'Kwc_Chained_CopyTarget_PagesGenerator';
            }
        }
        $ret['flags']['hasAllChainedByMaster'] = true;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['target'] = $this->getData()->getChildComponent('-target');
        return $ret;
    }

    public abstract function getTargetComponent();

    public static function getAllChainedByMasterFromChainedStart($componentClass, $master, $chainedType, $parentDataSelect = array())
    {
        if ($chainedType != 'Cc') return array();
        if (is_array($parentDataSelect)) {
            $parentDataSelect = new Kwf_Component_Select($parentDataSelect);
        }

        static $targets = array();
        static $targetIds = array();
        if (!isset($targets[$componentClass])) {
            $targets[$componentClass] = array();
            $targetIds[$componentClass] = array();
            $s = clone $parentDataSelect;
            $s->whereSubroot($master);
            foreach (Kwf_Component_Data_Root::getInstance()->getComponentsBySameClass($componentClass, $s) as $c) {
                $target = $c->getComponent()->getTargetComponent();
                if (!$target) continue;
                $targets[$componentClass][] = array(
                    'target' => $target,
                    'chainedStart' => $c
                );
                $targetIds[$componentClass][] = $target->componentId;
            }
        }
        $c = $master;
        while ($c) {
            if (in_array($c->componentId, $targetIds[$componentClass])) {
                break;
            }
            $c = $c->parent;
        }
        if (!$c) return array(); //shortcut: $master is not below $target

        $ret = array();
        foreach ($targets[$componentClass] as $t) {
            $target = $t['target'];
            $chainedStart = $t['chainedStart'];
            $m = $master;
            $targetReached = false;
            $ids = array();
            while ($m) {
                $pos = max(
                    strrpos($m->componentId, '-'),
                    strrpos($m->componentId, '_')
                );
                $id = substr($m->componentId, $pos);
                if ($m->componentId == $target->componentId) {
                    $targetReached = true;
                    break;
                }
                if ((int)$id > 0) { // nicht mit is_numeric wegen Bindestrich, das als minus interpretiert wird
                    $id = '_' . $id;
                }
                $m = $m->parent;
                if ($m) {
                    $ids[] = $id;
                }
            }
            if (!$targetReached) continue;
            $chained = $chainedStart; //->getChildComponent('-target');
            foreach (array_reverse($ids) as $id) {
                $parentDataSelect->whereId($id);
                $chained = $chained->getChildComponent($parentDataSelect);
                if (!$chained) break;
            }
            if ($chained) $ret[] = $chained;
        }
        return $ret;
    }
}
