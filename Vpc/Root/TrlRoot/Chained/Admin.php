<?php
class Vpc_Root_TrlRoot_Chained_Admin extends Vpc_Abstract_Admin
{
    //TODO sollte nicht mehr static sein wenn todo in Vps_Util_Component::duplicate erledigt wurde
    public static function duplicated(Vps_Component_Data $source, Vps_Component_Data $new)
    {
        $chained = Vps_Component_Data_Root::getInstance()
            ->getComponentsByClass('Vpc_Root_TrlRoot_Chained_Component', array('ignoreVisible'=>true)); //bySameClass wenn fkt nicht mehr static (todo oben erledigt)

        $sourceChained = array();
        foreach ($chained as $c) {
            $subRootAboveTrl = $c; //eg. domain
            while($subRootAboveTrl = $subRootAboveTrl->parent) {
                if (Vpc_Abstract::getFlag($subRootAboveTrl->componentClass, 'subroot')) {
                    break;
                }
            }
            if (!$subRootAboveTrl) {
                $subRootAboveTrl = Vps_Component_Data_Root::getInstance();
            }
            $d = $source;
            while($d = $d->parent) {
                if ($d->componentId == $subRootAboveTrl->componentId) {
                    $sourceChained[$c->getLanguage()] = $c;
                }
            }
        }

        $targetChained = array();
        foreach ($chained as $c) {
            $subRootAboveTrl = $c; //eg. domain
            while($subRootAboveTrl = $subRootAboveTrl->parent) {
                if (Vpc_Abstract::getFlag($subRootAboveTrl->componentClass, 'subroot')) {
                    break;
                }
            }
            if (!$subRootAboveTrl) {
                $subRootAboveTrl = Vps_Component_Data_Root::getInstance();
            }
            $d = $new;
            while($d = $d->parent) {
                if ($d->componentId == $subRootAboveTrl->componentId) {
                    if (isset($sourceChained[$c->getLanguage()])) { //only if there is a source language
                        $targetChained[] = array(
                            'targetChained' => $c,
                            'sourceChained' => $sourceChained[$c->getLanguage()],
                        );
                    }
                }
            }
        }

        foreach ($targetChained as $c) {
            $sourceChained = Vpc_Chained_Trl_Component::getChainedByMaster($source, $c['sourceChained'], array('ignoreVisible'=>true));
            $newChained = Vpc_Chained_Trl_Component::getChainedByMaster($new, $c['targetChained'], array('ignoreVisible'=>true));
            if (!$sourceChained || $source->componentId==$sourceChained->componentId) {
                continue; //wenn sourceChained nicht gefunden handelt es sich zB um ein MasterAsChild - was ignoriert werden muss
            }
            if (!$newChained) {
                throw new Vps_Exception("can't find chained components");
            }
            Vpc_Admin::getInstance($newChained->componentClass)->duplicate($sourceChained, $newChained);
        }
    }
}
