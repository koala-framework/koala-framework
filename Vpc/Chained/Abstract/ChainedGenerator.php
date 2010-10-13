<?php
class Vpc_Chained_Abstract_ChainedGenerator extends Vps_Component_Generator_PseudoPage_Table
{
    protected $_idColumn = 'id';
    protected $_hasNumericIds = false;
    protected $_inherits = true;
    protected $_filenameColumn = 'filename';

    public function getPagesControllerConfig($component, $generatorClass = null)
    {
        $ret = parent::getPagesControllerConfig($component, $generatorClass);
        $ret['icon'] = 'layout_content';
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

    public function getGeneratorFlags()
    {
        $ret = parent::getGeneratorFlags();
        $ret['showInPageTreeAdmin'] = true;
        return $ret;
    }
}