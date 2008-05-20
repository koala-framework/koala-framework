<?php
class Vpc_Forum_User_View_Guestbook_Write_Success_Component
extends Vpc_Formular_Success_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['profileUrl'] = $this->getParentComponent()->getParentComponent()->getUrl();
        return $ret;
    }
}
