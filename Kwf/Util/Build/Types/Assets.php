<?php
class Kwf_Util_Build_Types_Assets extends Kwf_Util_Build_Types_Abstract
{
    protected function _build($options)
    {
        $cmd = 'NODE_PATH=vendor/koala-framework/koala-framework/node_modules_build ./vendor/bin/node  node_modules/.bin/webpack --colors';
        if (!isset($_SERVER['NO_PROGRESS'])) $cmd .= ' --progress';
        passthru($cmd, $retVal);
        if ($retVal) {
            throw new Kwf_Exception("webpack failed");
        }
    }

    public function getTypeName()
    {
        return 'assets';
    }
}
