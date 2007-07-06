<?php
class Vps_Controller_Action_Admin_Components extends Vps_Controller_Action
{
    public function actionAction()
    {
        $table = new Vps_Dao_Components();
        $components = $table->getAvailableComponents();
    }

}
