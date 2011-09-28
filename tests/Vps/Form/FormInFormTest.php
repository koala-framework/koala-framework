 <?php
 /**
  * @group Vps_Form
  */
class Vps_Form_FormInFormTest extends Vps_Test_TestCase
{
    public function testForm()
    {
        $form = new Vps_Form('form1');
        $form->setModel(new Vps_Model_FnF(array('data'=>array(
            array('id'=>10, 'test1'=>'foo')
        ))));
        $form->add(new Vps_Form_Field_TextField('test1'));

        $textField2 = new Vps_Form_Field_TextField('test2');
        $form->add(new Vps_Form('form2'))
            ->setModel(new Vps_Model_FnF(array('data'=>array(
                array('id'=>10, 'test2'=>'bar')
            ))))
            ->setIdTemplate('{0}')
            ->add($textField2);

        $this->assertEquals('test2', $textField2->getName());
        $this->assertEquals('form1_form2_test2', $textField2->getFieldName());

        $this->assertNotNull($form->fields['test1']);
        $this->assertNotNull($form->fields['test2']);
        $this->assertNotNull($form->fields['form2']);
        $form->setId(10);
        $data = $form->load(null);
        $this->assertEquals($data, array('form1_test1'=>'foo', 'form1_form2_test2'=>'bar'));

        $data = array('form1_test1'=>'foox', 'form1_form2_test2'=>'barx');
        $form->prepareSave(null, $data);
        $form->save(null, $data);
        $this->assertEquals($form->getRow(null)->test1, 'foox');
        $this->assertEquals($form->fields['form2']->getRow($form->getRow(null))->test2, 'barx');

    }

    public function testCreateOnFind()
    {
        $form = new Vps_Form('form1');
        $form->setModel(new Vps_Model_FnF(array('data'=>array(
            array('id'=>10, 'test1'=>'foo')
        ))));
        $form->add(new Vps_Form_Field_TextField('test1'));
        $form->add(new Vps_Form('form2'))
            ->setModel(new Vps_Model_FnF())
            ->setCreateMissingRow(true)
            ->setIdTemplate('{0}')
            ->add(new Vps_Form_Field_TextField('test2'));

        $form->setId(10);
        $data = $form->load(null);
        $this->assertEquals($data, array('form1_test1'=>'foo', 'form1_form2_test2'=>''));

        $data = array('form1_test1'=>'foox', 'form1_form2_test2'=>'barx');
        $form->prepareSave(null, $data);
        $form->save(null, $data);
        $this->assertEquals($form->getRow(null)->test1, 'foox');
        $this->assertEquals($form->fields['form2']->getRow($form->getRow(null))->test2, 'barx');
    }

    public function testFormDuplicateEntry()
    {
        $form = new Vps_Form('form1');
        $model = new Vps_Model_FnF();
        $form->setModel($model);

        $form2 = new Vps_Form('form2');
        $form2  ->setModel($model)
                ->setIdTemplate('{0}')
                ->add(new Vps_Form_Field_TextField('test2'));

        $form->add(new Vps_Form_Field_TextField('test1'));
        $form->add($form2);

        $this->assertNotNull($form->fields['test1']);
        $this->assertNotNull($form->fields['test2']);
        $this->assertNotNull($form->fields['form2']);

        $data = array('form1_test1'=>'foox', 'form1_form2_test2'=>'barx');
        $form->prepareSave(null, $data);
        $form->save(null, $data);
        $this->assertEquals(array(array('id'=>1, 'test1'=>'foox', 'test2'=>'barx')), $model->getData());

    }

}
