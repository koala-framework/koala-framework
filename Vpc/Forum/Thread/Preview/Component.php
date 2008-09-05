<?php
class Vpc_Forum_Thread_Preview_Component extends Vpc_Abstract
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['thread'] = $this->getData()->parent;
        $ret = array_merge($ret, $ret['thread']->getComponent()->getThreadVars());
        return $ret;
    }


}
