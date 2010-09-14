<?php
class Vpc_Root_TrlRoot_Chained_Admin extends Vpc_Abstract_Admin
{
    //TODO sollte nicht mehr static sein wenn todo in Vps_Util_Component::duplicate erledigt wurde
    public static function duplicated(Vps_Component_Data $source, Vps_Component_Data $new)
    {
        $chained = Vps_Component_Data_Root::getInstance()
            ->getComponentsByClass('Vpc_Root_TrlRoot_Chained_Component', array('ignoreVisible'=>true)); //bySameClass wenn fkt nicht static
        foreach ($chained as $c) {
            $sourceChained = Vpc_Chained_Trl_Component::getChainedByMaster($source, $c, array('ignoreVisible'=>true));
            $newChained = Vpc_Chained_Trl_Component::getChainedByMaster($new, $c, array('ignoreVisible'=>true));

            //kann vorkommen wenn es trl überschrieben und es keine 1:1 übersetzung gibt
            //zB newsletter
            if (!$sourceChained && !$newChained) continue;

            if (!$sourceChained) {
                throw new Vps_Exception("can't find source chained component");
            }
            if (!$newChained) {
                throw new Vps_Exception("can't find new chained component");
            }
            Vpc_Admin::getInstance($newChained->componentClass)->duplicate($sourceChained, $newChained);
        }
    }
}
