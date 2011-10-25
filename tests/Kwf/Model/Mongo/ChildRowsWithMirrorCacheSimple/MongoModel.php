<?php
class Kwf_Model_Mongo_ChildRowsWithMirrorCacheSimple_MongoModel extends Kwf_Model_MirrorCacheSimple
{
    public function __construct()
    {
        $options = array();
        $options['proxyModel'] = new Kwf_Model_Mongo_TestModel();
        $options['sourceModel'] = 'Kwf_Model_Mongo_ChildRowsWithMirrorCacheSimple_SourceModel';
        $options['dependentModels'] = array(
            'Children' => new Kwf_Model_RowsSubModel_MirrorCacheSimple(array(
                'proxyModel' => new Kwf_Model_Mongo_RowsSubModel(array(
                    'fieldName' => 'children'
                )),
                'sourceModel' => 'Kwf_Model_Mongo_ChildRowsWithMirrorCacheSimple_SourceChildModel',
                'referenceMap' => array(
                    'Mongo' => Kwf_Model_RowsSubModel_Interface::SUBMODEL_PARENT
                ),
                'exprs' => array(
                    'parent_foo' => new Kwf_Model_Select_Expr_Parent('Mongo', 'foo')
                )
            ))
        );
        parent::__construct($options);
    }
}
