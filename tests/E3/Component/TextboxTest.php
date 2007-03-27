<?php
class E3_Component_TextboxTest extends E3_Test
{
    private $_dao;

    public function setUp()
    {
        $this->_dao = $this->createDao();
    }

    public function testContent()
    {
        $db = $this->_dao->getDb();
        $db->beginTransaction();
        $textbox = new E3_Component_Textbox($this->_dao, 1);
        $templateVars = $textbox->getTemplateVars();
        $this->assertEquals(array('contentParts'=>array(array('type'=>'content',
                                    'content'=>'inhalt von home')),
                                  'template'=>'Textbox.html',
                                  'id'=>1),
                                  $templateVars);
        
        $textbox = new E3_Component_Textbox($this->_dao, -1);
        $templateVars = $textbox->getTemplateVars();
        $this->assertEquals(array('contentParts'=>array(array('type'=>'content',
                                    'content'=>null)),
                                  'template'=>'Textbox.html',
                                  'id'=>-1),
                                  $templateVars);

        $db->query("INSERT INTO component_textbox SET component_id=-1, content='unit-test-content'");
        $templateVars = $textbox->getTemplateVars();
        $this->assertEquals(array('contentParts'=>array(array('type'=>'content',
                                    'content'=>'unit-test-content')),
                                  'template'=>'Textbox.html',
                                  'id'=>-1),
                                  $templateVars);
    }
}

