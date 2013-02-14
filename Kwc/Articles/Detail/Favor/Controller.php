<?php
class Kwc_Articles_Detail_Favor_Controller extends Kwf_Controller_Action
{
    public function jsonFavouriteAction()
    {
        $authedUser = Kwf_Registry::get('userModel')->getAuthedUser();
        if ($authedUser) {
            $component = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($this->_getParam('componentId'));
            $article = $component = $component->parent->row;
            $model = Kwf_Model_Abstract::getInstance('Kwc_Articles_Detail_Favor_Model');
            $select = new Kwf_Model_Select();
            $select->whereEquals('user_id', $authedUser->id);
            $select->whereEquals('article_id', $article->id);
            $row = $model->getRow($select);
            if ($this->_getParam('is_favourite')) {
                if (!$row) {
                    $row = $model->createRow();
                    $row->user_id = $authedUser->id;
                    $row->article_id = $article->id;
                    $row->save();
                }
            } else {
                if ($row) {
                    $row->delete();
                }
            }
        }
    }

    protected function _isAllowedComponent()
    {
        return !!Kwf_Registry::get('userModel')->getAuthedUser();
    }
}
