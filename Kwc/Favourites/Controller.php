<?php
class Kwc_Favourites_Controller extends Kwf_Controller_Action
{
    public function jsonFavouriteAction()
    {
        $authedUser = Kwf_Registry::get('userModel')->getAuthedUser();
        if ($authedUser) {
            $modelClass = Kwc_Abstract::getSetting($this->_getParam('class'), 'favouritesModel');
            $model = Kwf_Model_Abstract::getInstance($modelClass);
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

    public function jsonGetFavouritesAction()
    {
        $rows = array();
        $authedUser = Kwf_Registry::get('userModel')->getAuthedUser();
        if ($authedUser) {
            $modelClass = Kwc_Abstract::getSetting($this->_getParam('class'), 'favouritesModel');
            $model = Kwf_Model_Abstract::getInstance($modelClass);
            $select = new Kwf_Model_Select();
            $select->whereEquals('user_id', $authedUser->id);
            $select->whereEquals('component_id', $this->_getParam('kwcFavouritesComponentIds'));
            foreach ($model->export(Kwf_Model_Abstract::FORMAT_ARRAY, $select, array('columns' => array('component_id'))) as $row) {
                $rows[] = $row['component_id'];
            }
        }
        $this->view->componentIds = $rows;
    }

    protected function _isAllowedComponent()
    {
        return !!Kwf_Registry::get('userModel')->getAuthedUser();
    }
}
