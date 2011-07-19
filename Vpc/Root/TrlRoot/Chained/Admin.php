<?php
class Vpc_Root_TrlRoot_Chained_Admin extends Vpc_Abstract_Admin
{
    //TODO sollte nicht mehr static sein wenn todo in Vps_Util_Component::duplicate erledigt wurde
    public static function duplicated(Vps_Component_Data $source, Vps_Component_Data $new)
    {
        $chained = Vps_Component_Data_Root::getInstance()
            ->getComponentsByClass('Vpc_Root_TrlRoot_Chained_Component', array('ignoreVisible'=>true)); //bySameClass wenn fkt nicht mehr static (todo oben erledigt)
        foreach ($chained as $c) {
            $sourceChained = Vpc_Chained_Trl_Component::getChainedByMaster($source, $c, array('ignoreVisible'=>true));
            $newChained = Vpc_Chained_Trl_Component::getChainedByMaster($new, $c, array('ignoreVisible'=>true));
            if (!$sourceChained || $source->componentId==$sourceChained->componentId) continue; //wenn sourceChained nicht gefunden handelt es sich zB um ein MasterAsChild - was ignoriert werden muss
            if (!$newChained) {
                throw new Vps_Exception("can't find chained components");
            }
            Vpc_Admin::getInstance($newChained->componentClass)->duplicate($sourceChained, $newChained);
        }
    }
}
