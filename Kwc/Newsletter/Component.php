<?php
class Kwc_Newsletter_Component extends Kwc_Directories_ItemPage_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['detail']['component'] = 'Kwc_Newsletter_Detail_Component';

        // wird von der Mail_Redirect gerendered
        $ret['generators']['unsubscribe'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Newsletter_Unsubscribe_Component',
            'name' => trlKwf('Unsubscribe')
        );
        // wird von der Mail_Redirect gerendered
        $ret['generators']['editSubscriber'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Newsletter_EditSubscriber_Component',
            'name' => trlKwf('Edit subscriber')
        );

        $ret['childModel'] = 'Kwc_Newsletter_Model';
        $ret['flags']['hasResources'] = true;
        $ret['componentName'] = trlKwfStatic('Newsletter');
        $ret['componentIcon'] = new Kwf_Asset('email');

        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_None';
        $ret['extConfigControllerIndex'] = 'Kwc_Directories_Item_Directory_ExtConfigEditButtons';

        $ret['contentSender'] = 'Kwc_Newsletter_ContentSender';

        return $ret;
    }
}
