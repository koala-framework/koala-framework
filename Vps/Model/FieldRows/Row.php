<?php
class Vps_Model_FieldRows_Row extends Vps_Model_Row_Abstract
{
    public function __construct($config)
    {
        parent::__construct($config);
    }

    public function save()
    {
        if (!$this->_data[$this->_getPrimaryKey()]) {
            $id = $this->_model->insert($this->_data);
            $this->_data[$this->_getPrimaryKey()] = $id;
        } else {
            $this->_model->update($this->_data[$this->_getPrimaryKey()],
                                    $this->_data);
        }
    }

    public function delete()
    {
        $this->_model->delete($this->_data[$this->_getPrimaryKey()]);
    }

}
