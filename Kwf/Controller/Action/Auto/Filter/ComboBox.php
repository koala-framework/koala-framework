<?php
class Vps_Controller_Action_Auto_Filter_ComboBox extends Vps_Controller_Action_Auto_Filter_Query
{
    protected $_type = 'ComboBox';

    protected function _init()
    {
        parent::_init();
        $this->_mandatoryProperties['data'] = null;
    }

    public function setData($rawData)
    {
        $data = array();
        foreach ($rawData as $key => $val) {
            if (!is_array($val)) {
                $val = array($key, $val);
            }
            $data[] = $val;
        }
        $this->setProperty('data', $data);
        return $this;
    }
}
