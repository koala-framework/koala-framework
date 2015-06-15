<?php
class Kwc_Articles_ReadRequired_Component extends Kwc_Abstract
{
    private $_requiredArticles;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        $ret['flags']['processInput'] = true;
        return $ret;
    }

    public function processInput($input)
    {
        if (isset($input['read'])) {
            $article = Kwf_Model_Abstract::getInstance('Kwc_Articles_Directory_Model')
                ->getRow($input['read']);
            if ($article) {
                $article->markRead();
            }
        }
        if ($this->_getRequiredArticles()->count() == 0) Kwf_Util_Redirect::redirect('/');
    }

    protected function _getRequiredArticles()
    {
        $user = Kwf_Model_Abstract::getInstance('Users')->getAuthedUser();
        if (!$user) return null;
        if (is_null($this->_requiredArticles)) {
            $model = Kwf_Model_Abstract::getInstance('Kwc_Articles_Directory_Model');
            $select = $model->select();
            $select->whereEquals('visible', true);
            $select->whereEquals('read_required', true);
            $select->order('date', 'ASC');
            $childSelect = new Kwf_Model_Select();
            $childSelect->whereEquals('user_id', $user->id);
            $select->where(new Kwf_Model_Select_Expr_Not(
                new Kwf_Model_Select_Expr_Child_Contains('Views', $childSelect)
            ));
            $this->_requiredArticles = $model->getRows($select);
        }
        return $this->_requiredArticles;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $articles = $this->_getRequiredArticles();
        $ret['count'] = $articles->count();
        $ret['article'] = Kwf_Component_Data_Root::getInstance()->getComponentByClass(
            'Kwc_Articles_Detail_Component', array('id' => $articles->current()->id)
        );
        return $ret;
    }

    // To be called in Kwc_User_Login_Component::_getUrlForRedirect()
    public function getRedirectUrl($postData)
    {
        $allowRedirect = !isset($postData['redirect']) || $postData['redirect'] == '/';
        if ($allowRedirect) {
            $requiredArticles = $this->_getRequiredArticles();
            if ($requiredArticles && $requiredArticles->count() > 0) {
                return $this->getData()->url;
            }
        }
        return null;
    }

}
