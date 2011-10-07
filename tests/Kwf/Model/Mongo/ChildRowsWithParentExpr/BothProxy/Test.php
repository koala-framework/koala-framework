<?php
/**
 * @group Model
 * @group Mongo
 * @group Mongo_ChildRowsWithParentExpr
 * @group Mongo_ChildRowsWithParentExpr_BothProxy
 * @group slow
 */
class Vps_Model_Mongo_ChildRowsWithParentExpr_BothProxy_Test extends Vps_Model_Mongo_ChildRowsWithParentExpr_Abstract_Test
{
    protected $_modelClass = 'Vps_Model_Mongo_ChildRowsWithParentExpr_BothProxy_MongoModel';
    protected $_parentModelClass = 'Vps_Model_Mongo_ChildRowsWithParentExpr_BothProxy_ParentModel';
}
