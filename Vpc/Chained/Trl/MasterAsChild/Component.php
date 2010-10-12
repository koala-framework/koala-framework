<?php
class Vpc_Chained_Trl_MasterAsChild_Component extends Vpc_Chained_Abstract_MasterAsChild_Component
{
    //wofür wird das benötigt?
    //habs *nicht* in Vpc_Chained_Abstract_MasterAsChild_Component gegeben da es da bei ingenieurbueros suche probleme verursacht hat
    //und zwar ist die page nicht die für die sendContent() aufgerufen wird sondern die child, und da fehlen dann die boxen und alles
    public function sendContent()
    {
        $this->getData()->getChildComponent('-child')->getComponent()->sendContent();
    }
}
