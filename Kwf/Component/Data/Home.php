<?php
class Kwf_Component_Data_Home extends Kwf_Component_Data
{
    public function __get($var)
    {
        if ($var == 'url') {
            $c = $this;
            while ($c) {
                if (Kwf_Component_Abstract::getFlag($c->componentClass, 'hasHome') && $c->isPseudoPage) {
                    return $c->_getPseudoPageUrl();
                }
                $c = $c->parent;
            }
            $urlPrefix = Kwf_Config::getValue('kwc.urlPrefix');
            return $urlPrefix ? $urlPrefix : '/';
        }
        return parent::__get($var);
    }

}
