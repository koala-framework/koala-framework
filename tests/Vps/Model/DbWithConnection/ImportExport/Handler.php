<?php
class Vps_Model_DbWithConnection_ImportExport_Handler extends Vps_Srpc_Handler_Model
{
    public function getModel()
    {
        $extraParams = $this->getExtraParams();
        $this->_model = new Vps_Model_DbWithConnection_ImportExport_Model(array(
            'table' => $extraParams['table']
        ));
        return parent::getModel();
    }
}
