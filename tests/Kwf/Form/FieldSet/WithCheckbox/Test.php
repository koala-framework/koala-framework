<?php
/**
 * @group Kwf_Form_FieldSet
 */
class Kwf_Form_FieldSet_WithCheckbox_Test extends Kwf_Test_TestCase
{
    private $_form;
    public function setUp()
    {
        parent::setUp();
        $m1 = new Kwf_Model_FnF();
        $form = new Kwf_Form();
        $form->setModel($m1);
        $fs = $form->add(new Kwf_Form_Container_FieldSet('fs'))
            ->setCheckboxToggle(true)
            ->setCheckboxName('fs');
        $fs->add(new Kwf_Form_Field_TextField('text'))
            ->setAllowBlank(false);

        $this->_form = $form;
    }

    public function testCheckboxNotSetTextCanBeEmpty()
    {
        $form = $this->_form;

        $post = array(
            'fs' => false,
            'text' => '',
        );
        $post = $form->processInput($form->getRow(), $post);
        $this->assertEquals(array(), $form->validate($form->getRow(), $post));
        $form->prepareSave(null, $post);
        $form->save(null, $post);
        $form->afterSave(null, $post);

        $row = $form->getModel()->getRow(new Kwf_Model_Select());
        $this->assertEquals(false, $row->fs);
        $this->assertEquals(null, $row->text);
    }

    public function testCheckboxSetTextCanNotBeEmpty()
    {
        $form = $this->_form;

        $post = array(
            'fs' => true,
            'text' => '',
        );
        $post = $form->processInput($form->getRow(), $post);
        $this->assertEquals(1, count($form->validate($form->getRow(), $post)));
    }

    public function testCheckboxSetTextGetsSaved()
    {
        $form = $this->_form;

        $post = array(
            'fs' => true,
            'fs-post' => true,
            'text' => 'foo',
        );
        $post = $form->processInput($form->getRow(), $post);
        $this->assertEquals(array(), $form->validate($form->getRow(), $post));
        $form->prepareSave(null, $post);
        $form->save(null, $post);
        $form->afterSave(null, $post);

        $row = $form->getModel()->getRow(new Kwf_Model_Select());
        $this->assertEquals(true, (bool)$row->fs);
        $this->assertEquals('foo', $row->text);
    }

    public function testCheckboxNotSetTextGetsNotSaved()
    {
        $form = $this->_form;

        $post = array(
            'fs' => false,
            'text' => 'foo',
        );
        $post = $form->processInput($form->getRow(), $post);
        $this->assertEquals(array(), $form->validate($form->getRow(), $post));
        $form->prepareSave(null, $post);
        $form->save(null, $post);
        $form->afterSave(null, $post);

        $row = $form->getModel()->getRow(new Kwf_Model_Select());
        $this->assertEquals(false, $row->fs);
        $this->assertEquals(null, $row->text);
    }
}
