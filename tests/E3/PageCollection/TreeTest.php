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

    public function testSetParentPage()
    {
		$pc = $this->_pc;
		$p1 = new E3_Component_Textbox(-1, $this->_dao);
		$p2 = new E3_Component_Textbox(-2, $this->_dao);

		$pc->addPage($p1, 'foo');
		$pc->addPage($p2, 'bar');
		$pc->setParentPage($p1, $pc->getRootPage());
		$pc->setParentPage($p2, $p1);

   		$page1 = $this->_pc->getPageByPath('/foo');
   		$page2 = $this->_pc->getPageByPath('/foo/bar');
    	
    	$this->assertEquals(-1, $page1->getComponentId());
    	$this->assertEquals(-2, $page2->getComponentId());
    }
    
    public function testGetParentPage()
    {
		// Setup
		$pc = $this->_pc;
		$p1 = new E3_Component_Textbox(-1, $this->_dao);
		$p2 = new E3_Component_Textbox(-2, $this->_dao);

		$pc->addPage($p1, 'foo');
		$pc->addPage($p2, 'bar');
		$pc->setParentPage($p1, $pc->getRootPage());
		$pc->setParentPage($p2, $p1);

   		// Seiten aus Datenbank
   		$page = $this->_pc->getPageByPath('/test1/test2');
   		$parentPage = $this->_pc->getParentPage($page);
    	$this->assertType('E3_Component_Decorator', $parentPage);

   		// Oben erstellte Seiten
   		$page = $this->_pc->getPageByPath('/foo/bar');
   		$parentPage = $this->_pc->getParentPage($page);
   		$home = $this->_pc->getParentPage($parentPage);
		// /foo
    	$this->assertEquals(-1, $parentPage->getComponentId());
    	// home
    	$this->assertEquals($pc->getRootPage()->getComponentId(), $home->getComponentId());
    	// parent von home
    	$this->assertNull($this->_pc->getParentPage($home));
    	// Seite nicht in Seitenbaum
    	$this->assertNull($this->_pc->getParentPage(new E3_Component_Textbox(-100, $this->_dao)));
    }

    public function testChildPages()
    {
   		$page = $this->_pc->getPageByPath("/test1");
   		$childPages = $this->_pc->getChildPages($page);
   		$this->assertEquals(1, sizeof($childPages));
    	$this->assertType('E3_Component_Textbox', $childPages[0]);
    }
    
}

