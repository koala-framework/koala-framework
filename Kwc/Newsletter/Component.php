<?php
class Kwc_Newsletter_Component extends Kwc_Directories_ItemPage_Directory_Component implements Kwf_Util_Maintenance_JobProviderInterface
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['detail']['component'] = 'Kwc_Newsletter_Detail_Component';

        // wird von der Mail_Redirect gerendered
        $ret['generators']['unsubscribe'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Newsletter_Unsubscribe_Component',
            'name' => trlKwfStatic('Unsubscribe')
        );
        // wird von der Mail_Redirect gerendered
        $ret['generators']['editSubscriber'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Newsletter_EditSubscriber_Component',
            'name' => trlKwfStatic('Edit subscriber')
        );

        $ret['childModel'] = 'Kwc_Newsletter_Model';
        $ret['flags']['hasResources'] = true;
        $ret['componentName'] = trlKwfStatic('Newsletter');
        $ret['componentIcon'] = 'email';
        $ret['flags']['skipFulltextRecursive'] = true;

        $ret['flags']['noIndex'] = true;

        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_None';
        $ret['extConfigControllerIndex'] = 'Kwc_Newsletter_ExtConfigEditButtons';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Newsletter/Panel.js';

        $ret['contentSender'] = 'Kwc_Newsletter_ContentSender';

        $ret['menuConfig'] = 'Kwc_Newsletter_MenuConfig';
        return $ret;
    }

    public static function getMaintenanceJobs()
    {
        return array(
            'Kwc_Newsletter_StartMaintenanceJob',
            'Kwc_Newsletter_DeleteUnsubscribedJob'
        );
    }
}
