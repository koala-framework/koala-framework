<?php
class Kwc_Advanced_SocialBookmarks_Inherit_Trl_Component extends Kwc_Advanced_SocialBookmarks_Inherit_Component
{
    protected function _getBookmarksComponent()
    {
        $d = $this->getData()->getParentPseudoPageOrRoot();
        while ($d) {
            if (($c = $d->getChildComponent('-'.$this->getData()->id))
                && is_instance_of($c->componentClass, 'Kwc_Advanced_SocialBookmarks_Trl_Component')
            ) {
                return $c->getChildComponent('-child');
            }
            $d = $d->getParentPseudoPageOrRoot();
        }
        return null;
    }
}
