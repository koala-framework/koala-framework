<?php
/**
 * @group Model
 * @group Model_RowsSubModelProxy
 */
class Vps_Model_RowsSubModelProxy_Test extends PHPUnit_Framework_TestCase
{
    public function testIt()
    {
        $model = new Vps_Model_FnF(array(
            'columns' => array(),
            'data'=>array(
                array('id'=>1, 'foo'=>'bar', 'data'=>serialize(
                        array(
                            'autoId'=>2,
                            'data'=>array(
                                array('id'=>1, 'blub'=>'blub1', 'foo'=>'foo'),
                                array('id'=>2, 'blub'=>'blub2')
                            )
                        )
                    )
                )
            ),
            'dependentModels' => array(
                'Child' => new Vps_Model_RowsSubModel_Proxy(array(
                    'proxyModel' => new Vps_Model_FieldRows(array(
                        'fieldName' => 'data',
                    )),
                )),
            )
        ));

        $row = $model->getRow(1);
        $this->assertEquals($row->foo, 'bar');
        $rows = $row->getChildRows('Child');
        $this->assertEquals(count($rows), 2);
        $this->assertEquals($rows->current()->blub, 'blub1');
        $this->assertEquals($rows->current()->id, 1);
        $rows->current()->foo = 'foo';
        $rows->current()->save();
        $row->blub1 = 'blub1';
        $row->save();

        $this->assertEquals($model->getData(), array(
            array('id'=>1, 'foo'=>'bar', 'data'=>serialize(
            array(
                'autoId'=>2,
                'data'=>array(
                    array('id'=>1, 'blub'=>'blub1', 'foo'=>'foo'),
                    array('id'=>2, 'blub'=>'blub2')
                )
            )),
            'blub1'=>'blub1')
        ));
    }
}
