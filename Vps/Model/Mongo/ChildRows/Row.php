<?php
class Vps_Model_Mongo_ChildRows_Row extends Vps_Model_Row_Data_Abstract implements Vps_Model_SubModel_Row_Interface
{
    protected $_parentRow;
    public function __construct(array $config)
    {
        $this->_parentRow = $config['parentRow'];
        parent::__construct($config);
    }
    public function getSubModelParentRow()
    {
        return $this->_parentRow;
    }


    protected function _beforeSave()
    {
        parent::_beforeSave();
        foreach ($this->getModel()->getExprColumns() as $name) {
            $this->$name = $this->getModel()->getExprValue($this, $name);
        }
        foreach ($this->getModel()->getProxyContainerModels() as $model) {
            foreach ($model->getExprColumns() as $name) {
                foreach ($model->getExistingRows() as $proxyRow) {
                    if ($proxyRow->getProxiedRow() === $this) {
                        $this->$name = $model->getExprValue($proxyRow, $name);
                    }
                }
            }
        }
    }
}
