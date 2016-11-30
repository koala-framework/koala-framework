<?php
class Kwc_Guestbook_ActivatePost_Component extends Kwc_Form_Success_Component
{
    protected $_newVisibleValue = 1;

    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['placeholder']['success'] = trlKwfStatic('The entry in your guestbook has been acitvated.');
        $ret['placeholder']['toGuestbook'] = trlKwfStatic('Use this link to get to your guestbook:');
        $ret['flags']['processInput'] = true;
        $ret['flags']['passMailRecipient'] = true;
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['guestbookComponent'] = $this->getData()->parent;
        return $ret;
    }

    public function processInput(array $postData)
    {
        if (!isset($postData['recipient']) || !isset($postData['post_id'])) {
            throw new Kwf_Exception_NotFound();
        }
        $recipient = Kwc_Mail_Redirect_Component::parseRecipientParam($postData['recipient']); //do nothing with recipient, just make sure it is a valid one

        $post = explode('.', $postData['post_id']);
        $postId = $post[0];
        if (Kwf_Util_Hash::hash($postId) != $post[1]) {
            throw new Kwf_Exception_AccessDenied();
        }

        $model = $this->getData()->parent->getComponent()->getChildModel();
        $postRow = $model->getRow($postId);
        if (!$postRow) {
            throw new Kwf_ClientException(trlKwf("This post does not exist anymore."));
        }

        $postRow->visible = $this->_newVisibleValue;
        $postRow->save();
    }
}
