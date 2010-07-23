<?php
class Vpc_Basic_Flash_Upload_Row extends Vps_Model_Proxy_Row
{
    public function duplicate(array $data = array())
    {
        $ret = parent::duplicate($data);
        $this->_duplicateDependentModel($ret, 'FlashVars');
        return $ret;
    }
}