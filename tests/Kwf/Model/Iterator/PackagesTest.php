<?php
/**
 * @group Model_Iterator_PackagesTest
 */
class Kwf_Model_Iterator_PackagesTest extends Kwf_Test_TestCase
{
    public function testIt()
    {
        $model = new Kwf_Model_FnF(array(
            'data' => array(
                array('id'=>1, 'foo'=>'bar'),
                array('id'=>2, 'foo'=>'bar'),
                array('id'=>3, 'foo'=>'bar'),
                array('id'=>4, 'foo'=>'bar'),
                array('id'=>5, 'foo'=>'bar'),
            )
        ));

        $ids = array();
        $it = new Kwf_Model_Iterator_Packages(new Kwf_Model_Iterator_Rows($model, new Kwf_Model_Select()), 2);
        foreach ($it as $row) {
            $ids[] = $row->id;
        }
        $this->assertEquals($ids, array(1,2,3,4,5));
    }
}
