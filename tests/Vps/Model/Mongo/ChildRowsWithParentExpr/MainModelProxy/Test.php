<?php
/**
 * @group Model
 * @group Mongo
 * @group Mongo_ChildRowsWithParentExpr
 * @group Mongo_ChildRowsWithParentExpr_MainModelProxy
 * @group slow
 */
class Vps_Model_Mongo_ChildRowsWithParentExpr_MainModelProxy_Test extends Vps_Model_Mongo_ChildRowsWithParentExpr_Abstract_Test
{
    protected $_modelClass = 'Vps_Model_Mongo_ChildRowsWithParentExpr_MainModelProxy_MogoModel';
    protected $_parentModelClass = 'Vps_Model_Mongo_ChildRowsWithParentExpr_MainModelProxy_ParentModel';
}
