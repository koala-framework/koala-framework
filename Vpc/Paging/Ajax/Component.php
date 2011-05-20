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
        $ret = parent::getPartialVars($partial, $nr, $info);
        $ret['componentAjaxConfig'] = $this->_getSetting('componentAjaxConfig');
        return $ret;
    }
}
