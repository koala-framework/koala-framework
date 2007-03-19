<?php
Zend_Loader::loadClass('E3_PageCollection_Exception');

class E3_PageCollection_TreeExceptionTest extends E3_ExceptionTest
{
    protected $_pc;
    protected $_dao;

    public function setUp()
    {
        $this->_dao = $this->createDao(); 
        $this->_pc = new E3_PageCollection_Tree($this->_dao);
		$this->setExpectedException('E3_PageCollection_Exception');
    }

    public function testAddPage1()
    {
		$component = new E3_Component_Textbox(-1, $this->_dao);
		$this->_pc->addPage($component, 'foo');
		$this->_pc->addPage($component, 'bar');
    }
    
    public function testAddPage2()
    {
		$c1 = new E3_Component_Textbox(-1, $this->_dao);
		$this->_pc->addPage($c1, '');
    }
    
    public function testSetWrongParentPage1()
    {
		$component = new E3_Component_Textbox(-1, $this->_dao);
    	$this->_pc->setParentPage($component, $this->_pc->getRootPage());
    }
    
    public function testSetWrongParentPage2()
    {
		$component1 = new E3_Component_Textbox(-1, $this->_dao);
		$component2 = new E3_Component_Textbox(-2, $this->_dao);
		$this->_pc->addPage($component1, 'foo');
    	$this->_pc->setParentPage($component1, $component2);
    }
    
    public function testSetWrongParentPage3()
    {
		$component = new E3_Component_Textbox(-1, $this->_dao);
		$this->_pc->addPage($component, 'foo');
    	$this->_pc->setParentPage($this->_pc->getRootPage(), $component);
    }
    
    public function testSetWrongParentPage4()
    {
		$component = new E3_Component_Textbox(-1, $this->_dao);
		$this->_pc->addPage($component, 'foo');
    	$this->_pc->setParentPage($component, $component);
    }

    public function testSetWrongParentPage5()
    {
		$component = new E3_Component_Textbox(-1, $this->_dao);
		$this->_pc->addPage($component, 'foo');
     	$this->_pc->setParentPage($component, $component);
    }
}

