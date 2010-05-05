<?php
class Vpc_Posts_Directory_Model extends Vps_Model_Db_Proxy
{
    protected $_table = 'vpc_posts';
    protected $_rowClass = 'Vpc_Posts_Directory_Row';
    protected $_toStringField = 'id';

    protected $_referenceMap = array(
        'User' => array(
            'column' => 'user_id',
            'refModelClass' => ''
        )
    );

    protected function _init()
    {
        $userModelClass = get_class(Vps_Registry::get('userModel'));
        $this->_referenceMap['User']['refModelClass']  = $userModelClass;

        $this->_siblingModels = array(
            new Vps_Model_Field(array(
                'fieldName' => 'data'
            ))
        );

        parent::_init();
    }

    public function getLastPost($dbId)
    {
        $sel = $this->select()
            ->whereEquals('component_id', $dbId)
            ->whereEquals('visible', 1)
            ->order('id', 'DESC');
        return $this->getRow($sel);
    }

    public function getNumPosts($dbId)
    {
        $sel = $this->select()
            ->whereEquals('component_id', $dbId)
            ->whereEquals('visible', 1);
        return $this->countRows($sel);
    }

    public function getNumReplies($dbId)
    {
        return $this->getNumPosts($dbId)-1;
    }
}
