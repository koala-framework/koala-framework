<?php
class Vpc_Export_Xml_Trl_Component extends Vpc_Chained_Trl_MasterAsChild_Component
{
    public function sendContent()
    {
        $this->getData()->getChildComponent('-child')->getComponent()->sendContent();
    }

    public function getExportData()
    {
        //eine ebene weiter raufspringen, wegen masterAsChild
        return $this->getData()->parent->getComponent()->getExportData();
    }
}
