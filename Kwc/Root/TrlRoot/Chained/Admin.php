<?php
class Kwc_Root_TrlRoot_Chained_Admin extends Kwc_Abstract_Admin
{
    //TODO sollte nicht mehr static sein wenn todo in Kwf_Util_Component::duplicate erledigt wurde
    public static function duplicated(Kwf_Component_Data $source, Kwf_Component_Data $new)
    {
        $chained = Kwf_Component_Data_Root::getInstance()
            ->getComponentsByClass('Kwc_Root_TrlRoot_Chained_Component', array('ignoreVisible'=>true)); //bySameClass wenn fkt nicht mehr static (todo oben erledigt)
        foreach ($chained as $c) {
            $sourceChained = Kwc_Chained_Trl_Component::getChainedByMaster($source, $c, array('ignoreVisible'=>true));
            $newChained = Kwc_Chained_Trl_Component::getChainedByMaster($new, $c, array('ignoreVisible'=>true));
            if (!$sourceChained || $source->componentId==$sourceChained->componentId) continue; //wenn sourceChained nicht gefunden handelt es sich zB um ein MasterAsChild - was ignoriert werden muss
            if (!$newChained) {
                throw new Kwf_Exception("can't find chained components");
            }
            Kwc_Admin::getInstance($newChained->componentClass)->duplicate($sourceChained, $newChained);
        }
    }
}
