<?php
class Kwf_Util_RtrList
{
    /**
     * Checks mail addresses against the rtr-ecg list
     *
     * TODO: make this an implementation of an interface
     *
     * @param array $emails The emails that should be checked
     * @return array $result The clean array without the rtr-matched addresses
     */
    static public function getBadKeys(array $emails)
    {
        $cfg = Kwf_Registry::get('config');
        $client = new Kwf_Srpc_Client(array(
            'serverUrl' => $cfg->service->rtrlist->url
        ));
        return $client->getBadKeys($emails);
    }
}
