<?php
class Kwf_Util_Build_Types_Events extends Kwf_Util_Build_Types_Abstract
{
    protected function _build($options)
    {
        Kwf_Events_Dispatcher::clearCache();

        if (!file_exists('build/events')) {
            mkdir('build/events');
        }
        $fileName = 'build/events/listeners';
        if (file_exists($fileName)) unlink($fileName);
        $data = Kwf_Events_Dispatcher::getAllListeners();
        file_put_contents($fileName, serialize($data));
    }

    public function getTypeName()
    {
        return 'events';
    }
}
