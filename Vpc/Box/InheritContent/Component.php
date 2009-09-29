<?php
class Vpc_Box_InheritContent_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => false
        );

        //TODO: viewcache nicht deaktiveren
        //cache löschen muss dazu korrekt eingebaut werden
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $page = $this->getData();
        do {
            while ($page && !$page->inherits) {
                $page = $page->parent;
                if ($page instanceof Vps_Component_Data_Root) break;
            }
            $c = $page->getChildComponent('-'.$this->getData()->id)
                    ->getChildComponent(array('generator' => 'child'));
            if ($page instanceof Vps_Component_Data_Root) break;
            $page = $page->parent;
        } while(!$c->hasContent());
        $ret['child'] = $c;
        return $ret;
    }
    public function hasContent()
    {
        return true;
    }
}
