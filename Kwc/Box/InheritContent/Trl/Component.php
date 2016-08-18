<?php
class Kwc_Box_InheritContent_Trl_Component extends Kwc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['viewCache'] = false;
        $ret['ownModel'] = 'Kwf_Component_FieldModel';

        $ret['hasVisible'] = false; //set to true if exactly one form exists in child and visible checkbox should be added by InheritContent_Trl
        $ret['editComponents'] = array('child');

        $ret['extConfig'] = 'Kwc_Box_InheritContent_Trl_ExtConfig';

        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = Kwc_Abstract::getTemplateVars($renderer);
        $ret['template'] = self::getTemplateFile($this->getData()->chained->componentClass);
        $ret['child'] = $this->_getContentChild();
        return $ret;
    }

    private function _getContentChild()
    {
        $model = $this->getOwnModel();
        $masterChild = $this->getData()->chained->getComponent()->getContentChild();
        $c = Kwc_Chained_Trl_Component::getChainedByMaster($masterChild, $this->getData());
        $page = $this->getData();
        while(
            $c && (
                !$c->hasContent() ||
                ($this->_getSetting('hasVisible') && $c->parent->getComponent()->getRow() && !$c->parent->getComponent()->getRow()->visible)
            )
        ) {
            while ($page && !$page->inherits) {
                $page = $page->parent;
                if ($page instanceof Kwf_Component_Data_Root) break;
            }
            if (!isset($page->chained)) {
                $c = null;
                break;
            }
            $masterChild = $page->chained->getChildComponent('-'.$this->getData()->id)
                    ->getChildComponent(array('generator' => 'child'));
            $c = Kwc_Chained_Trl_Component::getChainedByMaster($masterChild, $this->getData());
            if ($page instanceof Kwf_Component_Data_Root) break;
            $page = $page->parent;
        }
        return $c;
    }

    public function getExportData()
    {
        return $this->_getContentChild()->getComponent()->getExportData();
    }
}
