<?php
class Kwf_Assets_Dependency_Dynamic_FacebookAppId extends Kwf_Assets_Dependency_File
{
    public function __construct()
    {
        parent::__construct(null);
    }

    public function getMimeType()
    {
        return 'text/javascript';
    }

    public function getContents($language)
    {
        $appId = Kwf_Registry::get('config')->kwc->fbAppData->appId;
        return "Kwf.FacebookAppId = $appId;\n";
    }

    public function getFileName()
    {
        return null;
    }

}
