<?php
class Vpc_Chained_Trl_Generator extends Vpc_Chained_Abstract_Generator
{
    public function getGeneratorFlags()
    {
        $ret = parent::getGeneratorFlags();
        if (is_instance_of($this->_class, 'Vpc_Root_TrlRoot_Chained_Component')) {
            $ret['trlBase'] = true;
        }
        return $ret;
    }
}