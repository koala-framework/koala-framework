<?php
/**
 * @group Vpc_FormDynamic
 */
class Vpc_FormDynamic_Basic_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_FormDynamic_Basic_Root');
    }

    public function testIt()
    {
        $c = $this->_root->getComponentById('root_form-form')->getComponent();
        $postData = array(
            'form_root_form-paragraphs-1' => '',
            'form_root_form-paragraphs-2' => 'asdfasdf',
            'form_root_form-paragraphs-3' => '',
            'form_root_form-paragraphs-4' => 'Def',
            'form_root_form-paragraphs-5-post' => '1',
            'form_root_form-paragraphs-6' => 'on',
            'form_root_form-paragraphs-6-post' => '1',
            'root_form-form' => 'submit',
            'root_form-form-post' => 'post',
        );
        $c->processInput($postData);
        $this->assertEquals($c->getErrors(), array());
        $row = $c->getFormRow();

        $text = $row->sent_mail_content_text;
        $this->assertContains('Required: asdfasdf', $text);
        $this->assertContains('Check: off', $text);
        $this->assertContains('CheckDefault: on', $text);
    }
}
