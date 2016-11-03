<?php
class Kwf_Util_ClearCache_Types_Symfony extends Kwf_Util_ClearCache_Types_Abstract
{
    protected function _clearCache($options)
    {
        passthru("./symfony/bin/console cache:clear --quiet");
    }

    public function getTypeName()
    {
        return 'symfony';
    }
    public function doesRefresh() { return false; }
    public function doesClear() { return true; }
}
