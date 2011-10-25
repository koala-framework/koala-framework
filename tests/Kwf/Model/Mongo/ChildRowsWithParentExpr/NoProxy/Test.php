<?php
/**
 * @group Model
 * @group Mongo
 * @group Mongo_ChildRowsWithParentExpr
 * @group Mongo_ChildRowsWithParentExpr_NoProxy
 * @group slow
 */
class Kwf_Model_Mongo_ChildRowsWithParentExpr_NoProxy_Test extends Kwf_Model_Mongo_ChildRowsWithParentExpr_Abstract_Test
{
    protected $_modelClass = 'Kwf_Model_Mongo_ChildRowsWithParentExpr_NoProxy_MongoModel';
    protected $_parentModelClass = 'Kwf_Model_Mongo_ChildRowsWithParentExpr_NoProxy_ParentModel';
}
