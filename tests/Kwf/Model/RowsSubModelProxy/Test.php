<?php
/**
 * @group Model
 * @group Model_RowsSubModelProxy
 */
class Kwf_Model_RowsSubModelProxy_Test extends Kwf_Test_TestCase
{
    public function testIt()
    {
        $model = new Kwf_Model_FnF(array(
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
                'Child' => new Kwf_Model_RowsSubModel_Proxy(array(
                    'proxyModel' => new Kwf_Model_FieldRows(array(
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
