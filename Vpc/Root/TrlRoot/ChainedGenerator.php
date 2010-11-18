<?php
class Vpc_Root_TrlRoot_ChainedGenerator extends Vps_Component_Generator_PseudoPage_Table
{
    protected $_idColumn = 'filename';
    protected $_hasNumericIds = false;
    protected $_inherits = true;

    public function getPagesControllerConfig($component, $generatorClass = null)
    {
        $ret = parent::getPagesControllerConfig($component, $generatorClass);
        $ret['icon'] = 'font';
        $ret['actions']['visible'] = true;
        if (!$component->visible) {
            $ret['iconEffects'][] = 'invisible';
        }
        $ret['iconEffects'][] = 'chained';
        return $ret;
    }

    protected function _getParentDataByRow($row, $select)
    {
        return Vps_Component_Data_Root::getInstance()->getComponentsByClass($this->_class);
    }

    protected function _formatSelect($parentData, $select)
    {
        $ret = parent::_formatSelect($parentData, $select);
        if (!$ret) return $ret;
        $ret->whereEquals('master', false);
        return $ret;
    }

    protected function _formatConfig($parentData, $row)
    {
        $data = parent::_formatConfig($parentData, $row);
        $data['language'] = $row->filename;
        $data['visible'] = isset($row->visible) ? $row->visible : true;

        //vielleicht flexibler machen?
        $data['chained'] = Vps_Component_Data_Root::getInstance()
                    ->getComponentByClass(Vpc_Abstract::getSetting($data['componentClass'], 'masterComponentClass'));
        return $data;
    }
}
