<?php
class Kwc_User_BoxWithoutLogin_UserModel extends Kwf_User_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
                'columns' => array('id', 'name', 'email'),
                'primaryKey' => 'id',
                'data'=> array(
                    array('id'=>1, 'name'=>'User 1', 'email'=>'bh@vivid-planet.com'),
                )
            ));
        Kwf_Auth::getInstance()->getStorage()->write(array(
            'userId' => 1
        ));
        parent::__construct($config);
    }

    public function setAuthedUser($idOrUserRow)
    {
        if (!is_object($idOrUserRow)) {
            $select = new Kwf_Model_Select();
            $select->where(new Kwf_Model_Select_Expr_Equal('id', $idOrUserRow));
            $idOrUserRow = $this->getRow($select);
        }
        parent::setAuthedUser($idOrUserRow);
    }

    public function hasAuthedUser()
    {
        return (bool)$this->_authedUser;
    }
}
