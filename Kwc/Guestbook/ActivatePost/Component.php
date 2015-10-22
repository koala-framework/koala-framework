<?php
class Kwc_Guestbook_ActivatePost_Component extends Kwc_Form_Success_Component
{
    protected $_newVisibleValue = 1;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['success'] = trlKwfStatic('The entry in your guestbook has been acitvated.');
        $ret['placeholder']['toGuestbook'] = trlKwfStatic('Use this link to get to your guestbook:');
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['guestbookComponent'] = $this->getData()->parent;
        return $ret;
    }


    public function processMailRedirectInput($recipient, $params)
    {
        $model = $this->getData()->parent->getComponent()->getChildModel();
        if (!empty($params['post_id']) && is_numeric($params['post_id'])) {
            $postRow = $model->getRow($params['post_id']);
        }
        if (!isset($postRow)) {
            throw new Kwf_ClientException(trlKwf("This post does not exist anymore."));
        }

        $postRow->visible = $this->_newVisibleValue;
        $postRow->save();
    }
}
