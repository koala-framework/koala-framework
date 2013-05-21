<?php
class Kwf_Form_FilterField_Test extends Kwf_Test_TestCase
{
    public function testFieldNames()
    {
        $form = new Kwf_Form();
        $form->setModel(new Kwf_Model_FnF(array(
            'data' => array(
                array('id'=>1, 'foo'=>2)
            )
        )));
        $foo = new Kwf_Form_Field_Select('foo', 'Foo');
        $foo->setValues('/kwf/test/kwf_form_filter-field_remote/json-data');
        $foo->setAllowBlank(false);

        $foo2 = new Kwf_Form_Field_Select('foo2', 'Foo2');
        $foo2->setValues(array(
                1 => 'filter1',
                2 => 'filter2',
        ))
        ->setSave(false)
        ->setAllowBlank(false);

        $form->add(new Kwf_Form_Field_FilterField())
            ->setFilterColumn('filter_id')
            ->setFilteredField($foo)
            ->setFilterField($foo2);

        $this->assertEquals('foo', $foo->getFieldName());
        $this->assertEquals('foo2', $foo2->getFieldName());
    }

    public function testFieldNamesWithFormName()
    {
        $form = new Kwf_Form();
        $form->setName('bar');
        $form->setModel(new Kwf_Model_FnF(array(
            'data' => array(
                array('id'=>1, 'foo'=>2)
            )
        )));
        $foo = new Kwf_Form_Field_Select('foo', 'Foo');
        $foo->setValues('/kwf/test/kwf_form_filter-field_remote/json-data');
        $foo->setAllowBlank(false);

        $foo2 = new Kwf_Form_Field_Select('foo2', 'Foo2');
        $foo2->setValues(array(
                1 => 'filter1',
                2 => 'filter2',
        ))
        ->setSave(false)
        ->setAllowBlank(false);

        $form->add(new Kwf_Form_Field_FilterField())
            ->setFilterColumn('filter_id')
            ->setFilteredField($foo)
            ->setFilterField($foo2);

        $this->assertEquals('bar_foo', $foo->getFieldName());
        $this->assertEquals('bar_foo2', $foo2->getFieldName());
    }
}
