<?php
class Vpc_Newsletter_Detail_Mail_Paragraphs_LinkTag_EditSubscriber_Component
    extends Vpc_Newsletter_Detail_Mail_Paragraphs_LinkTag_Unsubscribe_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => trlVps('Newsletter Subscriber settings')
        ));
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $nlData = $this->_getNewsletterComponent();
        $ret['editSubscriber'] = $nlData->getChildComponent('-editSubscriber');
        return $ret;
    }
}
