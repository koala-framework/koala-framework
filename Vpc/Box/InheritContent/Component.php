<?php
class Vpc_Box_InheritContent_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['modelname'] = 'Vps_Model_FnF';
        $ret['generators']['child'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => false
        );
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $page = $this->getData()->getPageOrRoot();
        do {
            $c = $page->getChildComponent('-'.$this->getData()->id)
                    ->getChildComponent(array('generator' => 'child'));
            if ($page instanceof Vps_Component_Data_Root) break;
        } while(!$c->hasContent() && $page = $page->getParentPageOrRoot());
        $ret['child'] = $c;
        return $ret;
    }
    public function hasContent()
    {
        return true;
    }
}
