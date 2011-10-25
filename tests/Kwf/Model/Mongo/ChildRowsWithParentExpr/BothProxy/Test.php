<?php
/**
 * @group Model
 * @group Mongo
 * @group Mongo_ChildRowsWithParentExpr
 * @group Mongo_ChildRowsWithParentExpr_BothProxy
 * @group slow
 */
class Kwf_Model_Mongo_ChildRowsWithParentExpr_BothProxy_Test extends Kwf_Model_Mongo_ChildRowsWithParentExpr_Abstract_Test
{
    protected $_modelClass = 'Kwf_Model_Mongo_ChildRowsWithParentExpr_BothProxy_MongoModel';
    protected $_parentModelClass = 'Kwf_Model_Mongo_ChildRowsWithParentExpr_BothProxy_ParentModel';
}
