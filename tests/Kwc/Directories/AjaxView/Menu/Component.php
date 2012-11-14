<?php
class Kwc_Directories_AjaxView_Menu_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $c = $this->getData()->getParentByClass('Kwc_Directories_AjaxView_Directory_Component');
        $ret['directory'] = $c;
        $ret['categories'] = $c->getChildComponent('-categories')->getChildComponents(array(
            'generator' => 'detail'
        ));
        return $ret;
    }

}
