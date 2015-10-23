<?php
class Kwc_User_Login_Trl_Component extends Kwc_Abstract_Composite_Trl_Component
{
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        if ($ret['register']) $ret['register'] = self::getChainedByMaster($ret['register'], $this->getData());
        if ($ret['lostPassword']) $ret['lostPassword'] = self::getChainedByMaster($ret['lostPassword'], $this->getData());
        return $ret;
    }

    public final function getUrlForRedirect($postData, $user) {
        return $this->_getUrlForRedirect($postData, $user);
    }

    protected function _getUrlForRedirect($postData, $user)
    {
        if (!empty($postData['redirect']) && substr($postData['redirect'], 0, 1) == '/') {
            $url = $postData['redirect'];
        } else {
            $url = Kwf_Component_Data_Root::getInstance()
                ->getChildPage(array('home' => true, 'subroot' => $this->getData()), array())
                ->url;
        }
        return $url;
    }
}
