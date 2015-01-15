<?php
class Kwc_Newsletter_Detail_Mail_Paragraphs_LinkTag_EditSubscriber_Component
    extends Kwc_Newsletter_Detail_Mail_Paragraphs_LinkTag_Unsubscribe_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => trlKwfStatic('Newsletter Subscriber settings')
        ));
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $nlData = $this->_getNewsletterComponent();
        $ret['editSubscriber'] = $nlData->getChildComponent('_editSubscriber');
        return $ret;
    }
}
