<?php
class Vps_Model_Mongo_ChildRowsWithMirrorCacheSimple_MongoModel extends Vps_Model_MirrorCacheSimple
{
    public function __construct()
    {
        $options = array();
        $options['proxyModel'] = new Vps_Model_Mongo_TestModel();
        $options['sourceModel'] = 'Vps_Model_Mongo_ChildRowsWithMirrorCacheSimple_SourceModel';
        $options['dependentModels'] = array(
            'Children' => new Vps_Model_RowsSubModel_MirrorCacheSimple(array(
                'proxyModel' => new Vps_Model_Mongo_RowsSubModel(array(
                    'fieldName' => 'children'
                )),
                'sourceModel' => 'Vps_Model_Mongo_ChildRowsWithMirrorCacheSimple_SourceChildModel',
                'referenceMap' => array(
                    'Mongo' => Vps_Model_RowsSubModel_Interface::SUBMODEL_PARENT
                ),
                'exprs' => array(
                    'parent_foo' => new Vps_Model_Select_Expr_Parent('Mongo', 'foo')
                )
            ))
        );
        parent::__construct($options);
    }
}
