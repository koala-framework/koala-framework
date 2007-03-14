<?php
class E3_Component_TextboxTest extends E3_Test
{
    public function setUp()
    {
    }

    public function testPaths()
    {
        $dao = $this->createDao();
        $db = $dao->getDb();
        $mocDao = $this->getMock('E3_Dao', array(), array('db'=>$db));

        $row = (object)array('content'=>'testContent');

        $textboxDao = $this->getMock('E3_Dao_Textbox', array(), array('db'=>array('db'=>$db)));
        $textboxDao->expects($this->any())
                   ->method('find')
                   ->with($this->equalTo(1))
                   ->will($this->returnValue($row));

        $mocDao->expects($this->any())
               ->method('getTable')
               ->with($this->equalTo('E3_Dao_Textbox'))
               ->will($this->returnValue($textboxDao));
        $textbox = new E3_Component_Textbox(1, $mocDao);
        $templateVars = $textbox->getTemplateVars();
        $this->assertEquals(array('content'=>'testContent',
                                  'template'=>'Textbox.html'),
                                  $templateVars);
    }
}

