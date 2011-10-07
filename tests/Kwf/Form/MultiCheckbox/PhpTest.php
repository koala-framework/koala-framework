<?php
/**
 * @group Kwf_Form_MultiCheckbox
 */
class Kwf_Form_MultiCheckbox_PhpTest extends Kwf_Test_TestCase
{
    public function testRelation()
    {
        $m1 = new Kwf_Form_MultiCheckbox_DataModel();
        $form = new Kwf_Form();
        $form->setModel($m1);
        $mcb = $form->add(new Kwf_Form_Field_MultiCheckbox(
            'Relation', 'Value', 'MultiCheck'
        ));

        $post = array(
            $mcb->getFieldName().'_1' => 0,
            $mcb->getFieldName().'_2' => 1,
            $mcb->getFieldName().'_3' => 1
        );
        $post = $form->processInput($form->getRow(), $post);
        $form->validate($form->getRow(), $post);
        $form->prepareSave(null, $post);
        $form->save(null, $post);
        $form->afterSave(null, $post);

        $rows = $m1->getRow(1)->getChildRows('Relation')->toArray();
        $expected = array(
            'id' => 1,
            'data_id' => 1,
            'values_id' => 3
        );
        $this->assertEquals(2, count($rows));
        $this->assertEquals($expected, $rows[0]);
        $expected['id'] = 2;
        $expected['values_id'] = 2;
        $this->assertEquals($expected, $rows[1]);
    }

    public function testWithRelModel()
    {
        $m1 = new Kwf_Form_MultiCheckbox_DataModelNoRel();
        $m2 = new Kwf_Form_MultiCheckbox_RelationModelNoRel();
        $form = new Kwf_Form();
        $form->setModel($m1);
        $mcb = $form->add(new Kwf_Form_Field_MultiCheckbox(
            $m2, 'Value', 'MultiCheck2'
        ));

        $post = array(
            $mcb->getFieldName().'_1' => 1,
            $mcb->getFieldName().'_2' => 0,
            $mcb->getFieldName().'_3' => 0
        );
        $post = $form->processInput($form->getRow(), $post);
        $form->validate($form->getRow(), $post);
        $form->prepareSave(null, $post);
        $form->save(null, $post);
        $form->afterSave(null, $post);

        $rows = $m2->getRows($m2->select()->whereEquals('data_id', 1))->toArray();
        $expected = array(
            'id' => 1,
            'data_id' => 1,
            'values_id' => 1
        );
        $this->assertEquals(1, count($rows));
        $this->assertEquals($expected, $rows[0]);
    }
}
