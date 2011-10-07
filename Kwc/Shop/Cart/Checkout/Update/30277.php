<?php
class Vpc_Shop_Cart_Checkout_Update_30277 extends Vps_Update
{
    protected function _init()
    {
        parent::_init();

        $this->_actions[] = new Vps_Update_Action_Db_AddField(array(
            'table' => 'vpc_shop_orders',
            'field' => 'firstname',
            'type' => 'VARCHAR(200)',
            'null' => false,
            'default' => ''
        ));
        $this->_actions[] = new Vps_Update_Action_Db_AddField(array(
            'table' => 'vpc_shop_orders',
            'field' => 'lastname',
            'type' => 'VARCHAR(200)',
            'null' => false,
            'default' => ''
        ));
        $this->_actions[] = new Vps_Update_Action_Db_AddField(array(
            'table' => 'vpc_shop_orders',
            'field' => 'city',
            'type' => 'VARCHAR(200)',
            'null' => false,
            'default' => ''
        ));
        $this->_actions[] = new Vps_Update_Action_Db_AddField(array(
            'table' => 'vpc_shop_orders',
            'field' => 'email',
            'type' => 'VARCHAR(200)',
            'null' => false,
            'default' => ''
        ));
        $this->_actions[] = new Vps_Update_Action_Db_AddField(array(
            'table' => 'vpc_shop_orders',
            'field' => 'zip',
            'type' => 'VARCHAR(50)',
            'null' => false,
            'default' => ''
        ));
        $this->_actions[] = new Vps_Update_Action_Db_AddField(array(
            'table' => 'vpc_shop_orders',
            'field' => 'payment',
            'type' => 'VARCHAR(100)',
            'null' => false,
            'default' => ''
        ));

        $this->_actions[] = new Vps_Update_Action_Db_ConvertFieldModel(array(
            'table' => 'vpc_shop_orders',
            'fields' => array('firstname', 'lastname', 'payment', 'zip', 'email', 'city'),
        ));
    }
    public function update()
    {
        parent::update();
        Vps_Registry::get('db')->query("UPDATE `vpc_shop_orders` SET payment='prePayment' WHERE payment='prepayment'");
    }
}
