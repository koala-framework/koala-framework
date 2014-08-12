<?php
class Kwf_Util_Build_Types_Events extends Kwf_Util_Build_Types_Abstract
{
    protected function _build()
    {
        Kwf_Component_Events::clearCache();

        if (!file_exists('build/events')) {
            mkdir('build/events');
        }
        $fileName = 'build/events/listeners';
        if (file_exists($fileName)) unlink($fileName);
        $data = Kwf_Component_Events::getAllListeners();
        file_put_contents($fileName, serialize($data));
    }

    public function getTypeName()
    {
        return 'events';
    }
}
