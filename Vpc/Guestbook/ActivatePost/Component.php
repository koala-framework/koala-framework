<?php
class Vpc_Guestbook_ActivatePost_Component extends Vpc_Form_Success_Component
{
    protected $_newVisibleValue = 1;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['success'] = trlVpsStatic('The entry in your guestbook has been acitvated.');
        $ret['placeholder']['toGuestbook'] = trlVpsStatic('Use this link to get to your guestbook:');
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
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
            throw new Vps_ClientException(trlVps("This post does not exist anymore."));
        }

        $postRow->visible = $this->_newVisibleValue;
        $postRow->save();
    }
}
