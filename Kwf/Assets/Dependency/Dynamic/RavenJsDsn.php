<?php
class Kwf_Assets_Dependency_Dynamic_RavenJsDsn extends Kwf_Assets_Dependency_Abstract
{
    public function getMimeType()
    {
        return 'text/javascript';
    }

    public function getContents($language)
    {
        if (!Kwf_Config::getValue('ravenJs.dsn')) {
            throw new Kwf_Exception('ravenJS.dsn config setting is required');
        }
        $data = array(
            'dsn' => Kwf_Config::getValue('ravenJs.dsn')
        );
        return "Kwf.RavenJsConfig = ".json_encode($data).";";
    }

    public function usesLanguage()
    {
        return false;
    }

    public function getIdentifier()
    {
        return 'RavenJsDsn';
    }
}
