<?php
class Vps_Component_Cache_Directory_Root_TrlGenerator extends Vps_Component_Generator_Page_Static
{
    protected function _getParentDataByRow($row, $select)
    {
        return Vps_Component_Data_Root::getInstance()->getComponentsByClass($this->_class);
    }

    protected function _formatConfig($parentData, $row)
    {
        $data = parent::_formatConfig($parentData, $row);
        $data['language'] = 'en';
        $data['chained'] = Vps_Component_Data_Root::getInstance()
                    ->getComponentByClass(Vpc_Abstract::getSetting($data['componentClass'], 'masterComponentClass'));
        return $data;
    }
}
