<?php
class Kwc_Newsletter_Detail_Mail_Paragraphs_LinkTag_EditSubscriber_Component
    extends Kwc_Newsletter_Detail_Mail_Paragraphs_LinkTag_Unsubscribe_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret = array_merge(parent::getSettings($param), array(
            'componentName' => trlKwfStatic('Newsletter Subscriber settings')
        ));
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $nlData = $this->_getNewsletterComponent();
        $ret['editSubscriber'] = $nlData->getChildComponent('_editSubscriber');
        return $ret;
    }
}
