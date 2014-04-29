<?php
class Kwf_Util_Build_Types_ComponentEvents extends Kwf_Util_Build_Types_Abstract
{
    protected function _build()
    {
        Kwf_Component_Events::clearCache();

        $fileName = 'build/component/events';
        if (file_exists($fileName)) unlink($fileName);
        $data = Kwf_Component_Events::getAllListeners();
        file_put_contents($fileName, serialize($data));
    }

    public function getTypeName()
    {
        return 'componentEvents';
    }
}
