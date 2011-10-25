<?php
/**
 * @group Form_MultiFields
 */
class Kwf_Form_MultiFields_ChildFormTest extends Kwf_Test_TestCase
{
    public function testBam()
    {
        $m1 = new Kwf_Model_FnF();
        $m2 = new Kwf_Model_FnF();
        $m3 = new Kwf_Model_FnF();

        $form = new Kwf_Form();
        $form->setModel($m1);
        $form->add(new Kwf_Form_Field_TextField('test1'));
        $form->add(new Kwf_Form_Field_MultiFields($m2))
            ->setReferences(array(
                //TODO: sollte auch mit models automatisch funktionieren
                'columns' => array('test1_id'),
                'refColumns' => array('id'),
            ))
            ->fields->add(new Kwf_Form('child'))
                ->setCreateMissingRow(true)
                ->setIdTemplate('{id}')
                ->setModel($m3)
                ->add(new Kwf_Form_Field_TextField('test2'));

        $post = array(
            'test1' => 'blub',
            'Kwf_Model_FnF' => array(
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
