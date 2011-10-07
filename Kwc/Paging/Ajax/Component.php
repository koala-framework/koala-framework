<?php
class Kwc_Paging_Ajax_Component extends Kwc_Paging_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentAjaxConfig'] = array(
            'hideFx' => 'fadeOut',
            'showFx' => 'fadeIn'
        );
        $ret['assets']['dep'][] = 'KwfComponentAjax';
        return $ret;
    }

    public function getPartialVars($partial, $nr, $info)
    {
        $parent = $this->getData()->parent;
        $ret = parent::getPartialVars($partial, $nr, $info);
        $ret['componentAjaxConfig'] = $this->_getSetting('componentAjaxConfig');
        $ret['componentAjaxConfig']['contentClass'] =
            Kwf_Component_Abstract::formatCssClass($parent->componentClass);
        $ret['componentAjaxConfig']['componentId'] = $parent->componentId;
        return $ret;
    }
}
