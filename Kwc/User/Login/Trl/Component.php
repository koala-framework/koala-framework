<?php
class Kwc_User_Login_Trl_Component extends Kwc_Abstract_Composite_Trl_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        if ($ret['register']) $ret['register'] = self::getChainedByMaster($ret['register'], $this->getData());
        if ($ret['lostPassword']) $ret['lostPassword'] = self::getChainedByMaster($ret['lostPassword'], $this->getData());
        return $ret;
    }
}
