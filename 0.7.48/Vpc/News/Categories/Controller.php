<?php
class Vpc_News_Categories_Controller extends Vps_Controller_Action_Pool_PoolController
{

    protected function _getPool()
    {
        return Vpc_Abstract::getSetting($this->class, 'pool');
    }

}
