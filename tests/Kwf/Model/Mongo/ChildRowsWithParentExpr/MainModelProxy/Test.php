<?php
/**
 * @group Model
 * @group Mongo
 * @group Mongo_ChildRowsWithParentExpr
 * @group Mongo_ChildRowsWithParentExpr_MainModelProxy
 * @group slow
 */
class Kwf_Model_Mongo_ChildRowsWithParentExpr_MainModelProxy_Test extends Kwf_Model_Mongo_ChildRowsWithParentExpr_Abstract_Test
{
    protected $_modelClass = 'Kwf_Model_Mongo_ChildRowsWithParentExpr_MainModelProxy_MogoModel';
    protected $_parentModelClass = 'Kwf_Model_Mongo_ChildRowsWithParentExpr_MainModelProxy_ParentModel';
}
