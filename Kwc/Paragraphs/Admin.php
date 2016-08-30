<?php
class Kwc_Paragraphs_Admin extends Kwc_Admin
{
    public function componentToString(Kwf_Component_Data $c)
    {
        return Kwf_Trl::getInstance()->trlStaticExecute(Kwc_Abstract::getSetting($c->componentClass, 'componentName'));
    }
}
