<?php
class Vps_Model_Mongo_RowsSubModel_Row extends Vps_Model_Row_Data_Abstract
    implements Vps_Model_RowsSubModel_Row_Interface
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

    public function __set($name, $value)
    {
        if (in_array($name, $this->_model->getExprColumns())) {
            //TODO: expr column setzen nicht erlauben
            //ist im moment aber noch nötig wegen updaten der expr werte
            $n = $this->_transformColumnName($name);
            if ($this->$name !== $value) {
                $this->_setDirty(true);
            }
            $this->_data[$n] = $value;
            $this->_postSet($name, $value);
        } else {
            parent::__set($name, $value);
        }
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
            foreach ($model->getExistingRows() as $proxyRow) {
                if ($proxyRow->getProxiedRow() === $this) {
                    foreach ($model->getExprColumns() as $name) {
                        $expr = $model->getExpr($name);
                        if ($expr instanceof Vps_Model_Select_Expr_Parent) {
                            $ref = $model->getReference($expr->getParent());
                            if ($ref === Vps_Model_RowsSubModel_Interface::SUBMODEL_PARENT) {
                                continue;
                            }
                        }
                        $this->$name = $model->getExprValue($proxyRow, $name);
                    }
                }
            }
        }
    }
}
