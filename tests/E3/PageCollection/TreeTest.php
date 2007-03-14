<?php
class E3_PageCollection_TreeTest extends E3_Test
{
    protected $_pc;
    protected $_dao;

    public function setUp()
    {
        $this->_dao = $this->createDao(); 
        $this->_pc = new E3_PageCollection_Tree($this->_dao);
    }

    public function testPaths()
    {
   		$page = $this->_pc->getPageByPath("../");
    	$this->assertNull($page);
    	
   		$page = $this->_pc->getPageByPath("ä#ü+986#ä3");
    	$this->assertNull($page);
    	
   		$page = $this->_pc->getPageByPath("");
    	$this->assertType('E3_Component_Textbox', $page);
   		
   		$page = $this->_pc->getPageByPath("/");
    	$this->assertType('E3_Component_Textbox', $page);
    	
   		$page = $this->_pc->getPageByPath("/test1");
    	$this->assertType('E3_Component_Decorator', $page);

   		$page = $this->_pc->getPageByPath("/test1/test2");
    	$this->assertType('E3_Component_Textbox', $page);
    }

    public function testConstructedPaths()
    {
		$pc = $this->_pc;
		$component = new E3_Component_Textbox(10, $this->_dao);
		$pc->addPage($component, 'foo');
		$pc->setParentPage($component, $pc->getRootPage());
		
		$pc->getRootPage()->callGenerateHierarchy($pc);
   		
   		$page = $this->_pc->getPageByPath('/foo');
    	$this->assertType('E3_Component_Textbox', $page);
    }
    
    public function testAddPageAlreadyExistingComponentId()
    {
		$pc = $this->_pc;
		$component = new E3_Component_Textbox(10, $this->_dao);
		$pc->addPage($component, 'foo');
    	try {
			$component = new E3_Component_Textbox(10, $this->_dao);
			$pc->addPage($component, 'foo1');
    	} catch (E3_PageCollection_Exception $e) {
    		return;
    	}
    	$this->fail('An expected Exception has not been raised.');    	
    }
    public function testParentPage()
    {
   		$page = $this->_pc->getPageByPath("/test1/test2");
   		$parentPage = $this->_pc->getParentPage($page);
    	$this->assertType('E3_Component_Decorator', $parentPage);
    }

    public function testChildPages()
    {
   		$page = $this->_pc->getPageByPath("/test1");
   		$childPages = $this->_pc->getChildPages($page);
   		$this->assertEquals(1, sizeof($childPages));
    	$this->assertType('E3_Component_Textbox', $childPages[0]);
    }
}

