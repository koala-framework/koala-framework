<?php
class Vpc_Paging_Ajax_Component extends Vpc_Paging_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentAjaxConfig'] = array(
            'hideFx' => 'slideOut',
            'showFx' => 'slideIn'
        );
        $ret['assets']['dep'][] = 'VpsComponentAjax';
        return $ret;
    }

    public function getPartialVars($partial, $nr, $info)
    {
        $parent = $this->getData()->parent;
        $ret = parent::getPartialVars($partial, $nr, $info);
        $ret['componentAjaxConfig'] = $this->_getSetting('componentAjaxConfig');
        $ret['componentAjaxConfig']['contentClass'] =
            Vps_Component_Abstract::formatCssClass($parent->componentClass);
        $ret['componentAjaxConfig']['componentId'] = $parent->componentId;
        return $ret;
    }
}
