<?php
class Vps_Component_Data_Home extends Vps_Component_Data
{
    public function __get($var)
    {
        if ($var == 'url') {
            $c = $this;
            while ($c) {
                if (Vps_Component_Abstract::getFlag($c->componentClass, 'hasHome') && $c->isPseudoPage) {
                    return $c->_getPseudoPageUrl();
                }
                $c = $c->parent;
            }
            return '/';
        }
        return parent::__get($var);
    }

}
