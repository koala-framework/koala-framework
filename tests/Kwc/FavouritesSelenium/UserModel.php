<?php
class Kwc_FavouritesSelenium_UserModel extends Kwf_User_Model
{
    protected $_authedUserId = 1;

    public function __construct($config = array())
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
                'columns' => array('id', 'name'),
                'primaryKey' => 'id',
                'data'=> array(
                    array('id'=>1, 'name'=>'User 1'),
                    array('id'=>2, 'name'=>'User 2'),
                )
            ));
        parent::__construct($config);
    }

    public function getAuthedUser()
    {
        //return first user
        $select = new Kwf_Model_Select();
        $select->where(new Kwf_Model_Select_Expr_Equal('id', $this->_authedUserId));
        $row = $this->getRow($select);
        return $row;
    }

    public function setAuthedUser($id)
    {
        $this->_authedUserId = $id;
    }

    public function getAuthedUserId()
    {
        return $this->getAuthedUser()->id;
    }
}