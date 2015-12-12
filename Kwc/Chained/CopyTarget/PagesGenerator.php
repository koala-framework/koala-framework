<?php
class Kwc_Chained_CopyTarget_PagesGenerator extends Kwc_Root_Category_Cc_Generator
{
    protected function _getChainedGenerator()
    {
        $class = Kwc_Abstract::getSetting($this->_class, 'pageGeneratorComponentClass');
        return Kwf_Component_Generator_Abstract::getInstance($class, 'page');
    }

    protected function _getChainedChildComponents($parentData, $select)
    {
        $chainedData = $this->_getChainedData($parentData);
        if ($chainedData) {
            return $this->_getChainedGenerator()->getChildData(
                $chainedData, $this->_getChainedSelect($select)
            );
        } else {
            return array();
        }
    }

    protected function _getChainedData($data)
    {
        if ($data) {
            $parentClasses = Kwc_Abstract::getParentClasses($data->componentClass);
            if (in_array('Kwc_Chained_CopyTarget_Component' , $parentClasses)) {
                return $data->getComponent()->getTargetComponent();
            }
        }
        return parent::_getChainedData($data);
    }

    protected function _formatConfig($parentData, $row)
    {
        $ret = parent::_formatConfig($parentData, $row);
        $parentClasses = Kwc_Abstract::getParentClasses($parentData->componentClass);
        if (in_array('Kwc_Chained_CopyTarget_Component' , $parentClasses)) {
            $ret['parent'] = $parentData;
        }
        return $ret;
    }
}