<?php
class Vps_Model_Mongo_ChildRowsWithParentExpr_MongoModel extends Vps_Model_Mongo_TestModel
{
    protected function _init()
    {
        parent::_init();
        $this->_dependentModels['Foo'] = new Vps_Model_Mongo_ChildRows(array(
            'fieldName' => 'foo',
            'referenceMap' => array(
                'Parent' => array(
                    'refModelClass' => 'Vps_Model_Mongo_ChildRowsWithParentExpr_ParentModel',
                    'column' => 'parent_id'
                ),
                'Mongo' => Vps_Model_SubModel_Interface::SUBMODEL_PARENT,
            ),
            'exprs' => array(
//                 'parent_name' => new Vps_Model_Select_Expr_Parent('Parent', 'name'),

                'mongo_name' => new Vps_Model_Select_Expr_Parent('Mongo', 'name'),
            )
        ));
    }
}