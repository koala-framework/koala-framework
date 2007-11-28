<?php
class E3_Component_DecoratorTest extends E3_Test
{
    private $_dao;

    public function setUp()
    {
        $this->_dao = $this->createDao();
    }

    public function testPaths()
    {
        $db = $this->_dao->getDb();
        $decorator = new E3_Component_Decorator($this->_dao, 2);
        $templateVars = $decorator->getTemplateVars();
        //todo...
    }
}

