<?php
class Vpc_Root_Category_Cc_Generator extends Vpc_Chained_Cc_Generator
{
    public function getPagesControllerConfig($component)
    {
        $ret = parent::getPagesControllerConfig($component);
        foreach ($ret['actions'] as &$a) $a = false;
        return $ret;
    }

    protected function _getDataClass($config, $id)
    {
        if (isset($config['isHome']) && $config['isHome']) {
            return 'Vps_Component_Data_Home';
        } else {
            return parent::_getDataClass($config, $id);
        }
    }
}
