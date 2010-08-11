<?php
class Vpc_Advanced_SocialBookmarks_Inherit_Component extends Vpc_Abstract
{
    public function getTemplateVars()
    {
        $ret = array();
        $ret['linkTemplate'] = false;
        if ($c = $this->_getBookmarksComponent()) {
            $ret = $c->getComponent()->getTemplateVarsWithNetworks($this->getData()->parent);
            $ret['linkTemplate'] = self::getTemplateFile($c->componentClass);
        }
        return $ret;
    }

    protected function _getBookmarksComponent()
    {
        $d = $this->getData()->getParentPseudoPageOrRoot();
        while ($d) {
            if (($c = $d->getChildComponent('-'.$this->getData()->id))
                && is_instance_of($c->componentClass, 'Vpc_Advanced_SocialBookmarks_Component')
            ) {
                return $c;
            }
            $d = $d->getParentPseudoPageOrRoot();
        }
        return null;
    }

    public function getCacheMeta()
    {
        $ret = parent::getCacheMeta();
        if ($c = $this->_getBookmarksComponent()) {
            $ret[] = new Vps_Component_Cache_Meta_Component($c);
        }
        return $ret;
    }
}
