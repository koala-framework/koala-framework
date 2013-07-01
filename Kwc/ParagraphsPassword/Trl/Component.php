<?php
class Kwc_ParagraphsPassword_Trl_Component extends Kwc_Paragraphs_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['plugins'][] = 'Kwc_ParagraphsPassword_Plugin_Component';
        return $ret;
    }

    public function getPassword()
    {
        return $this->getData()->chained->getComponent()->getPassword();
    }
}
