<?php
class Kwc_Chained_Abstract_ChainedGenerator extends Kwf_Component_Generator_PseudoPage_Table
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

        if ($this->getModel()->hasColumn('visible')) {
            $ret['actions']['visible'] = true;
        }
        return $ret;
    }

    protected function _getParentDataByRow($row, $select)
    {
        return Kwf_Component_Data_Root::getInstance()->getComponentsByClass($this->_class);
    }

    protected function _formatConfig($parentData, $row)
    {
        $data = parent::_formatConfig($parentData, $row);
        $data['language'] = $row->filename;
        $data['visible'] = isset($row->visible) ? $row->visible : true;


        //vielleicht flexibler machen?
        //$data['chained'] = Kwf_Component_Data_Root::getInstance()
        //            ->getComponentByClass(Kwc_Abstract::getSetting($data['componentClass'], 'masterComponentClass'));

        $mastetCc = Kwc_Abstract::getSetting($data['componentClass'], 'masterComponentClass');
        $data['chained'] = $parentData->getChildComponent(array('componentClass'=>$mastetCc));

        return $data;
    }

    public function getGeneratorFlags()
    {
        $ret = parent::getGeneratorFlags();
        $ret['showInPageTreeAdmin'] = true;
        $ret['showInLinkInternAdmin'] = true;
        return $ret;
    }
}