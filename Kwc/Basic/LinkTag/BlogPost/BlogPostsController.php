<?php
class Kwc_Basic_LinkTag_BlogPost_BlogPostsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_permissions = array();
    protected $_modelName = 'Kwc_Blog_Directory_Model';
    protected $_defaultOrder = array('field'=>'publish_date', 'direction'=>'DESC');
    protected $_paging = 20;

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Kwf_Grid_Column('title'));
        $this->_columns->add(new Kwf_Grid_Column('publish_date'));
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('component_id', $this->_getParam('blogComponentId'));
        return $ret;
    }

    protected function _isAllowedComponent()
    {
        return !!Kwf_Registry::get('userModel')->getAuthedUser();
    }
}
