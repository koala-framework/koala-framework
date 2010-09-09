<?php
class Vps_Model_Mongo_ChildRows_Row extends Vps_Model_Row_Data_Abstract implements Vps_Model_RowsSubModel_Row_Interface
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
            $expr = $this->getModel()->getExpr($name);
            if ($expr instanceof Vps_Model_Select_Expr_Parent) {
                $ref = $this->getModel()->getReference($expr->getParent());
                if ($ref === Vps_Model_RowsSubModel_Interface::SUBMODEL_PARENT) {
                    continue;
                }
            }
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
