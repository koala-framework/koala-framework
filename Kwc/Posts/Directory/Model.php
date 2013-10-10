<?php
class Kwc_Posts_Directory_Model extends Kwf_Model_Db_Proxy
{
    protected $_table = 'kwc_posts';
    protected $_rowClass = 'Kwc_Posts_Directory_Row';
    protected $_toStringField = 'id';

    protected $_referenceMap = array(
        'User' => array(
            'column' => 'user_id',
            'refModelClass' => ''
        )
    );

    protected function _init()
    {
        $userModelClass = get_class(Kwf_Registry::get('userModel'));
        $this->_referenceMap['User']['refModelClass']  = $userModelClass;

        $this->_siblingModels = array(
            new Kwf_Model_Field(array(
                'fieldName' => 'data'
            ))
        );

        parent::_init();
    }

    public function getLastPost($dbId)
    {
        $sel = $this->select()
            ->whereEquals('visible', 1)
            ->order('id', 'DESC');
        if ($this->hasColumn('component_id')) {
            $sel->whereEquals('component_id', $dbId);
        }
        return $this->getRow($sel);
    }

    public function getNumPosts($dbId)
    {
        $sel = $this->select()
            ->whereEquals('visible', 1);
        if ($this->hasColumn('component_id')) {
            $sel->whereEquals('component_id', $dbId);
        }
        return $this->countRows($sel);
    }

    public function getNumReplies($dbId)
    {
        return $this->getNumPosts($dbId)-1;
    }

    public function hasColumn($col)
    {
        if ($col == 'component_id') {
            return in_array($col, $this->getOwnColumns()); // component_id must not be in siblings
        }
        if ($col == 'component') {
            return false;
        }
        return parent::hasColumn($col);
    }
}
