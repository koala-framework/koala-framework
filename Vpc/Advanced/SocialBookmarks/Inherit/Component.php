<?php
class Vpc_Advanced_SocialBookmarks_Inherit_Component extends Vpc_Abstract
{
    public function getTemplateVars()
    {
        $ret = array();
        $ret['linkTemplate'] = false;
        $d = $this->getData()->getParentPseudoPageOrRoot();
        while ($d) {
            if (($c = $d->getChildComponent('-'.$this->getData()->id))
                && is_instance_of($c->componentClass, 'Vpc_Advanced_SocialBookmarks_Component')
            ) {
                $ret = $c->getComponent()->getTemplateVars();
                $ret['linkTemplate'] = self::getTemplateFile($c->componentClass);
                $ret['networks'] = $c->getComponent()->getNetworks($this->getData()->parent);
                break;
            }
            $d = $d->getParentPseudoPageOrRoot();
        }
        return $ret;
    }
}
