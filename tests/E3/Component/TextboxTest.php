<?php
class E3_Component_TextboxTest extends E3_Test
{
    private $_dao;

    public function setUp()
    {
        $this->_dao = $this->createDao();
    }

    public function testPaths()
    {
        $db = $this->_dao->getDb();
        $textbox = new E3_Component_Textbox(1, $this->_dao);
        $templateVars = $textbox->getTemplateVars();
        $this->assertEquals(array('content'=>'inhalt von home',
                                  'template'=>'Textbox.html'),
                                  $templateVars);
        
        $textbox = new E3_Component_Textbox(-1, $this->_dao);
        $templateVars = $textbox->getTemplateVars();
        $this->assertEquals(array('content'=>null,
                                  'template'=>'Textbox.html'),
                                  $templateVars);

        $db->query("INSERT INTO component_textbox SET component_id=-1, content='unit-test-content'");
        $templateVars = $textbox->getTemplateVars();
        $this->assertEquals(array('content'=>'unit-test-content',
                                  'template'=>'Textbox.html'),
                                  $templateVars);
    }
}

