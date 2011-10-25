<?php
class Kwf_Model_Mongo_ChildRowsWithParentExpr_MainModelProxy_MogoModel extends Kwf_Model_Proxy
{
    protected function _init()
    {
        $this->_proxyModel = new Kwf_Model_Mongo_TestModel();
        $this->_dependentModels['Foo'] = new Kwf_Model_Mongo_RowsSubModel(array(
            'parentModel' => $this,
            'fieldName' => 'foo',
            'referenceMap' => array(
                'Parent' => array(
                    'refModelClass' => 'Kwf_Model_Mongo_ChildRowsWithParentExpr_MainModelProxy_ParentModel',
                    'column' => 'parent_id'
                ),
                'Mongo' => Kwf_Model_RowsSubModel_Interface::SUBMODEL_PARENT,
            ),
            'exprs' => array(
                'parent_name' => new Kwf_Model_Select_Expr_Parent('Parent', 'name'),
                'mongo_name' => new Kwf_Model_Select_Expr_Parent('Mongo', 'name'),
            )
        ));
        parent::_init();
    }
}