<?php
class Kwc_Favourites_Controller extends Kwf_Controller_Action
{
    public function jsonFavouriteAction()
    {
        $authedUser = Kwf_Registry::get('userModel')->getAuthedUser();
        if ($authedUser) {
            $model = Kwf_Model_Abstract::getInstance('Kwc_Favourites_Model');
            $select = new Kwf_Model_Select();
            $select->whereEquals('user_id', $authedUser->id);
            $select->whereEquals('component_id', $this->_getParam('componentId'));
            $row = $model->getRow($select);
            if ($this->_getParam('is_favourite')) {
                if (!$row) {
                    $row = $model->createRow();
                    $row->user_id = $authedUser->id;
                    $row->component_id = $this->_getParam('componentId');
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
