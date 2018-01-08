<?php
class Kwc_Favourites_UserModel extends Kwf_User_Model
{
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

    public function setAuthedUser($idOrUserRow)
    {
        if (!is_object($idOrUserRow)) {
            $select = new Kwf_Model_Select();
            $select->where(new Kwf_Model_Select_Expr_Equal('id', $idOrUserRow));
            $idOrUserRow = $this->getRow($select);
        }
        parent::setAuthedUser($idOrUserRow);
    }
}

