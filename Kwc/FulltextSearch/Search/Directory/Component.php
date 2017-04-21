<?php
class Kwc_FulltextSearch_Search_Directory_Component extends Kwc_Directories_Item_Directory_Component implements Kwf_Util_Maintenance_JobProviderInterface
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Fulltext Search');
        $ret['generators']['detail']['class'] = 'Kwc_FulltextSearch_Search_Directory_Generator';
        $ret['generators']['detail']['component'] = 'Kwc_FulltextSearch_Search_Detail_Component';
        $ret['generators']['child']['component']['view'] = 'Kwc_FulltextSearch_Search_ViewAjax_Component';
        $ret['childModel'] = 'Kwc_FulltextSearch_Search_Directory_Model';
        $ret['flags']['usesFulltext'] = true;
        $ret['flags']['noIndex'] = true;
        $ret['updateTags'] = array('fulltext');
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_None';
        return $ret;
    }

    public static function validateSettings($settings, $componentClass)
    {
        parent::validateSettings($settings, $componentClass);
        if (!file_exists('solr/solr.xml')) throw new Kwf_Exception('solr.xml file missing. Please configure solr correctly!');
        $solrXml = simplexml_load_file('solr/solr.xml');
        foreach (Kwf_Config::getValue('kwc.domains') as $key => $value) {
            if (!count($solrXml->xpath('/solr/cores/core[@name="'.$key.'"]'))) {
                throw new Kwf_Exception("solr.xml core for $key missing. Please configure solr correctly!");
            }
        }
    }

    public static function getMaintenanceJobs()
    {
        return array(
            'Kwc_FulltextSearch_Search_Directory_MaintenanceJobs_CheckContents',
            'Kwc_FulltextSearch_Search_Directory_MaintenanceJobs_UpdateChanged',
        );
    }
}
