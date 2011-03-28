<?php
class Vps_Model_Mongo_ChildRowsWithParentExpr_BothProxy_MongoModel extends Vps_Model_Proxy
{
    protected function _init()
    {
        $this->_proxyModel = new Vps_Model_Mongo_TestModel();
        $this->_dependentModels['Foo'] = new Vps_Model_Mongo_RowsSubModel(array(
            'parentModel' => $this,
            'fieldName' => 'foo',
            'referenceMap' => array(
                'Parent' => array(
                    'refModelClass' => 'Vps_Model_Mongo_ChildRowsWithParentExpr_BothProxy_ParentModel',
                    'column' => 'parent_id'
                ),
                'Mongo' => Vps_Model_RowsSubModel_Interface::SUBMODEL_PARENT,
            ),
            'exprs' => array(
                'parent_name' => new Vps_Model_Select_Expr_Parent('Parent', 'name'),
                'mongo_name' => new Vps_Model_Select_Expr_Parent('Mongo', 'name'),
            )
        ));
        parent::_init();
    }
}
