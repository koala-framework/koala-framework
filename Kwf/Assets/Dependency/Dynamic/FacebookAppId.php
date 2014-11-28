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
        $appIds = array();
        foreach (Kwf_Component_Abstract::getComponentClasses() as $class) {
            if (!Kwc_Abstract::getFlag($class, 'hasBaseProperties')) continue;

            $subRoots = Kwf_Component_Data_Root::getInstance()->getComponentsByClass($class);
            foreach ($subRoots as $subRoot) {
                if ($appId = $subRoot->getBaseProperty('fbAppData.appId')) {
                    $appIds[$subRoot->componentId] = $appId;
                }
            }
        }
        if (empty($appIds)) throw new Kwf_Exception('No Facebook App ID found');

        return "Kwf.FacebookAppIds = " . json_encode($appIds) . ";\n";
    }

    public function getFileName()
    {
        return null;
    }

}
