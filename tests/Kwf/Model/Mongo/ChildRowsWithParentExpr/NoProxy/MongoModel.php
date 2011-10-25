<?php
class Kwf_Model_Mongo_ChildRowsWithParentExpr_NoProxy_MongoModel extends Kwf_Model_Mongo_TestModel
{
    protected function _init()
    {
        $this->_dependentModels['Foo'] = new Kwf_Model_Mongo_RowsSubModel(array(
            'parentModel' => $this,
            'fieldName' => 'foo',
            'referenceMap' => array(
                'Parent' => array(
                    'refModelClass' => 'Kwf_Model_Mongo_ChildRowsWithParentExpr_NoProxy_ParentModel',
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