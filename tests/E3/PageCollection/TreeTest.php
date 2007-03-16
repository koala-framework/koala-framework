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
    	
   		$page = $this->_pc->getPageByPath("�+986#�");
    	$this->assertNull($page);
    	
   		$page = $this->_pc->getPageByPath("//////");
    	$this->assertEquals($page, $this->_pc->getRootPage());

   		$page = $this->_pc->getPageByPath("");
    	$this->assertEquals($page, $this->_pc->getRootPage());
    	
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

    public function testGetChildPages()
    {
		// Setup
		$pc = $this->_pc;
		$p1 = new E3_Component_Textbox(-1, $this->_dao);
		$p2 = new E3_Component_Textbox(-2, $this->_dao);
		$p3 = new E3_Component_Textbox(-3, $this->_dao);
		$p4 = new E3_Component_Textbox(-4, $this->_dao);

		// root -> foo -> bar -> {barchild1, barchild2}
		$pc->addPage($p1, 'foo');
		$pc->addPage($p2, 'bar');
		$pc->addPage($p3, 'barchild1');
		$pc->addPage($p4, 'barchild2');
		$pc->setParentPage($p1, $pc->getRootPage());
		$pc->setParentPage($p2, $p1);
		$pc->setParentPage($p3, $p2);
		$pc->setParentPage($p4, $p2);

   		// Seiten aus Datenbank
   		$page = $this->_pc->getPageByPath("/test1");
   		$childPages = $this->_pc->getChildPages($page);
   		$this->assertEquals(1, sizeof($childPages));
    	$this->assertType('E3_Component_Textbox', $childPages[0]);
    	$childPage = $this->_pc->getChildPage($page, 'test2');
    	$this->assertEquals($childPages[0], $childPage);
   		
   		// Seiten aus Datenbank
   		$page = $this->_pc->getPageByPath("/test1");
   		$childPages = $this->_pc->getChildPages($p1);
   		$this->assertEquals(1, sizeof($childPages));
    	$this->assertEquals(-2, $childPages[0]->getComponentId());
   		$childPages = $this->_pc->getChildPages($p2);
   		$this->assertEquals(2, sizeof($childPages));
    	$this->assertEquals(-3, $childPages[0]->getComponentId());
    	$this->assertEquals(-4, $childPages[1]->getComponentId());
    }

    public function testGenerateHierarchyWithAndWithoutFilename()
    {
		$pc = $this->_pc;
		$rootPage = $pc->getRootPage();
		$rootPage->callGenerateHierarchy($pc, 'test1');
        $rootPage->callGenerateHierarchy($pc);
    }
}

