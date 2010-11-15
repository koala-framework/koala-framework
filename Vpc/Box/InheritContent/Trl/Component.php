<?php
class Vpc_Box_InheritContent_Trl_Component extends Vpc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['viewCache'] = false;
        $ret['ownModel'] = 'Vps_Component_FieldModel';

        $childConfig = Vpc_Admin::getInstance($ret['generators']['child']['component']['child'])->getExtConfig();
        if (array_keys($childConfig) == array('form')) {
            $ret['hasVisible'] = true;
            $ret['editComponents'] = array();
        } else {
            $ret['hasVisible'] = false;
            $ret['editComponents'] = array('child');
        }

        $ret['extConfig'] = 'Vpc_Box_InheritContent_Trl_ExtConfig';

        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = Vpc_Abstract::getTemplateVars();
        $ret['linkTemplate'] = self::getTemplateFile($this->getData()->chained->componentClass);
        $ret['child'] = $this->_getContentChild();
        return $ret;
    }

    private function _getContentChild()
    {
        $model = $this->getOwnModel();
        $masterChild = $this->getData()->chained->getComponent()->getContentChild();
        $c = Vpc_Chained_Trl_Component::getChainedByMaster($masterChild, $this->getData());
        $page = $this->getData();
        while($c && (!$c->hasContent() || ($this->_getSetting('hasVisible') && !$c->parent->getComponent()->getRow()->visible))) {
            while ($page && !$page->inherits) {
                $page = $page->parent;
                if ($page instanceof Vps_Component_Data_Root) break;
            }
            if (!isset($page->chained)) {
                $c = null;
                break;
            }
            $masterChild = $page->chained->getChildComponent('-'.$this->getData()->id)
                    ->getChildComponent(array('generator' => 'child'));
            $c = Vpc_Chained_Trl_Component::getChainedByMaster($masterChild, $this->getData());
            if ($page instanceof Vps_Component_Data_Root) break;
            $page = $page->parent;
        }
        return $c;
    }

    public function getExportData()
    {
        return $this->_getContentChild()->getComponent()->getExportData();
    }
}
