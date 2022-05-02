<?php
class Kwc_Root_TrlRoot_Chained_Admin extends Kwc_Abstract_Admin
{
    //TODO sollte nicht mehr static sein wenn todo in Kwf_Util_Component::duplicate erledigt wurde
    public static function duplicated(Kwf_Component_Data $source, Kwf_Component_Data $new)
    {
        $chained = Kwf_Component_Data_Root::getInstance()
            ->getComponentsByClass('Kwc_Root_TrlRoot_Chained_Component', array('ignoreVisible'=>true)); //bySameClass wenn fkt nicht mehr static (todo oben erledigt)

        $sourceChained = array();
        foreach ($chained as $c) {
            $subRootAboveTrl = $c; //eg. domain
            while($subRootAboveTrl = $subRootAboveTrl->parent) {
                if (Kwc_Abstract::getFlag($subRootAboveTrl->componentClass, 'subroot')) {
                    break;
                }
            }
            if (!$subRootAboveTrl) {
                $subRootAboveTrl = Kwf_Component_Data_Root::getInstance();
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
                if (Kwc_Abstract::getFlag($subRootAboveTrl->componentClass, 'subroot')) {
                    break;
                }
            }
            if (!$subRootAboveTrl) {
                $subRootAboveTrl = Kwf_Component_Data_Root::getInstance();
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
            $sourceChained = Kwc_Chained_Trl_Component::getChainedByMaster($source, $c['sourceChained'], array('ignoreVisible'=>true));
            $newChained = Kwc_Chained_Trl_Component::getChainedByMaster($new, $c['targetChained'], array('ignoreVisible'=>true));
            if (!$sourceChained || $source->componentId==$sourceChained->componentId) {
                continue; //wenn sourceChained nicht gefunden handelt es sich zB um ein MasterAsChild - was ignoriert werden muss
            }
            if (!$newChained && $c['sourceChained']->componentId == $c['targetChained']->componentId) {
                continue;
            }
            if (!$newChained) {
                throw new Kwf_Exception("can't find chained components");
            }
            Kwc_Admin::getInstance($newChained->componentClass)->duplicate($sourceChained, $newChained);
        }
    }
}
