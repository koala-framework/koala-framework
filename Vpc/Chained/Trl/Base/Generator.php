<?php
class Vpc_Chained_Trl_Base_Generator extends Vpc_Chained_Trl_Generator
{
    protected function _getChainedData($data)
    {
        if ($data->componentClass == $this->_class) {
            return Vps_Component_Data_Root::getInstance()
                ->getComponentByClass(Vpc_Abstract::getSetting($this->_class, 'masterComponentClass'));
        }
        throw new Vps_Exception_NotYetImplemented();
    }

    public function getGeneratorFlags()
    {
        $ret = parent::getGeneratorFlags();
        $ret['trlBase'] = true;
        return $ret;
    }
}
