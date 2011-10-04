<?php
class Vpc_Chained_Trl_Generator extends Vpc_Chained_Abstract_Generator
{
    public function getGeneratorFlags()
    {
        $ret = parent::getGeneratorFlags();
        if (is_instance_of($this->_class, 'Vpc_Root_TrlRoot_Chained_Component')) {
            $ret['trlBase'] = true;
        }
        $ret['chainedType'] = 'Trl';
        return $ret;
    }

    protected function _getChainedSelect($select)
    {
        $select = parent::_getChainedSelect($select);
        $select->ignoreVisible(); // Visible ist bei Trl immer extra zu setzen
        return $select;
    }
}