<?php
class Vpc_Advanced_SocialBookmarks_Inherit_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        return $ret;
    }
    public function getTemplateVars()
    {
        $ret = array();
        $ret['linkTemplate'] = false;
        $d = $this->getData()->getParentPageOrRoot();
        while ($d) {
            if ($c = $d->getChildComponent(array('componentClass'=>'Vpc_Advanced_SocialBookmarks_Component'))) {
                $ret = $c->getComponent()->getTemplateVars();
                $ret['linkTemplate'] = self::getTemplateFile($c->componentClass);
                $ret['networks'] = $c->getComponent()->getNetworks($this->getData()->parent);
                break;
            }
            $d = $d->getParentPageOrRoot();
        }
        return $ret;
    }
}
