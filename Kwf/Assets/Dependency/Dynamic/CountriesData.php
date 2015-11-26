<?php
class Kwf_Assets_Dependency_Dynamic_CountriesData extends Kwf_Assets_Dependency_Abstract
{
    public function getMimeType()
    {
        return 'text/javascript';
    }

    public function getContentsPacked($language)
    {
        $data = array();
        $nameColumn = 'name_'.$language;
        foreach (Kwf_Model_Abstract::getInstance('Kwf_Util_Model_Countries')->getRows() as $row) {
            $data[] = array(
                'id' => $row->id,
                'name' => $row->$nameColumn
            );
        }
        $ret = "Kwf.CountriesData = ".json_encode($data).";\n";

        return Kwf_SourceMaps_SourceMap::createEmptyMap($ret);
    }

    public function usesLanguage()
    {
        return true;
    }

    public function getIdentifier()
    {
        return 'CountriesData';
    }
}
