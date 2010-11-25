<?php
/**
 * @group Form_MultiFields
 */
class Vps_Form_MultiFields_ChildFormTest extends Vps_Test_TestCase
{
    public function testBam()
    {
        $m1 = new Vps_Model_FnF();
        $m2 = new Vps_Model_FnF();
        $m3 = new Vps_Model_FnF();

        $form = new Vps_Form();
        $form->setModel($m1);
        $form->add(new Vps_Form_Field_TextField('test1'));
        $form->add(new Vps_Form_Field_MultiFields($m2))
            ->setReferences(array(
                //TODO: sollte auch mit models automatisch funktionieren
                'columns' => array('test1_id'),
                'refColumns' => array('id'),
            ))
            ->fields->add(new Vps_Form('child'))
                ->setCreateMissingRow(true)
                ->setIdTemplate('{id}')
                ->setModel($m3)
                ->add(new Vps_Form_Field_TextField('test2'));

        $post = array(
            'test1' => 'blub',
            'Vps_Model_FnF' => array(
                array('child_test2' => 'bab')
            )
        );
        $post = $form->processInput($form->getRow(), $post);
        $form->validate($form->getRow(), $post);
        $form->prepareSave(null, $post);
        $form->save(null, $post);
        $form->afterSave(null, $post);

        $r = $m1->getRow(1);
        $this->assertEquals('blub', $r->test1);

        $r = $m3->getRow(1);
        $this->assertEquals('bab', $r->test2);
    }
}
