<?ph
class E3_PageCollection_TreeTest extends E3_Tes

    protected $_pc
    protected $_dao

    public function setUp(
    
        $this->_dao = $this->createDao();
        $this->_pc = new E3_PageCollection_Tree($this->_dao)
    
   
    public function testPaths(
    
   		$page = $this->_pc->getPageByPath("../")
    	$this->assertNull($page)
    
   		$page = $this->_pc->getPageByPath("�+986#�")
    	$this->assertNull($page)
    
   		$page = $this->_pc->getPageByPath("//////")
    	$this->assertEquals($page, $this->_pc->getRootPage())

   		$page = $this->_pc->getPageByPath("")
    	$this->assertEquals($page, $this->_pc->getRootPage())
    
   		$page = $this->_pc->getPageByPath("")
    	$this->assertType('E3_Component_Textbox', $page)
   	
   		$page = $this->_pc->getPageByPath("/")
    	$this->assertType('E3_Component_Textbox', $page)
    
   		$page = $this->_pc->getPageByPath("/test1")
    	$this->assertType('E3_Component_Decorator', $page)

   		$page = $this->_pc->getPageByPath("/test1/test2")
    	$this->assertType('E3_Component_Textbox', $page)
    

    public function testSetParentPage(
    
		$pc = $this->_pc
		$p1 = new E3_Component_Textbox($this->_dao, -1)
		$p2 = new E3_Component_Textbox($this->_dao, -2)

		$pc->addPage($p1, 'foo')
		$pc->addPage($p2, 'bar')
		$pc->setParentPage($p1, $pc->getRootPage())
		$pc->setParentPage($p2, $p1)

   		$page1 = $this->_pc->getPageByPath('/foo')
   		$page2 = $this->_pc->getPageByPath('/foo/bar')
    
    	$this->assertEquals(-1, $page1->getId())
    	$this->assertEquals(-2, $page2->getId())
    
   
    public function testGetParentPage(
    
		// Setu
		$pc = $this->_pc
		$p1 = new E3_Component_Textbox($this->_dao, -1)
		$p2 = new E3_Component_Textbox($this->_dao, -2)

		$pc->addPage($p1, 'foo')
		$pc->addPage($p2, 'bar')
		$pc->setParentPage($p1, $pc->getRootPage())
		$pc->setParentPage($p2, $p1)

   		// Seiten aus Datenban
   		$page = $this->_pc->getPageByPath('/test1/test2')
   		$parentPage = $this->_pc->getParentPage($page)
    	$this->assertType('E3_Component_Decorator', $parentPage)

   		// Oben erstellte Seite
   		$page = $this->_pc->getPageByPath('/foo/bar')
   		$parentPage = $this->_pc->getParentPage($page)
   		$home = $this->_pc->getParentPage($parentPage)
		// /fo
    	$this->assertEquals(-1, $parentPage->getId())
    	// hom
    	$this->assertEquals($pc->getRootPage()->getId(), $home->getId())
    	// parent von hom
    	$this->assertNull($this->_pc->getParentPage($home))
    	// Seite nicht in Seitenbau
    	$this->assertNull($this->_pc->getParentPage(new E3_Component_Textbox($this->_dao, -100)))
    

    public function testGetChildPages(
    
		// Setu
		$pc = $this->_pc
		$p1 = new E3_Component_Textbox($this->_dao, -1)
		$p2 = new E3_Component_Textbox($this->_dao, -2)
		$p3 = new E3_Component_Textbox($this->_dao, -3)
		$p4 = new E3_Component_Textbox($this->_dao, -4)

		// root -> foo -> bar -> {barchild1, barchild2
		$pc->addPage($p1, 'foo')
		$pc->addPage($p2, 'bar')
		$pc->addPage($p3, 'barchild1')
		$pc->addPage($p4, 'barchild2')
		$pc->setParentPage($p1, $pc->getRootPage())
		$pc->setParentPage($p2, $p1)
		$pc->setParentPage($p3, $p2)
		$pc->setParentPage($p4, $p2)

   		// Seiten aus Datenban
   		$page = $this->_pc->getPageByPath("/test1")
   		$childPages = $this->_pc->getChildPages($page)
   		$this->assertEquals(1, sizeof($childPages))
    	$this->assertType('E3_Component_Textbox', $childPages[0])
    	$childPage = $this->_pc->getChildPage($page, 'test2')
    	$this->assertEquals($childPages[0], $childPage)
   	
   		// Seiten aus Datenban
   		$page = $this->_pc->getPageByPath("/test1")
   		$childPages = $this->_pc->getChildPages($p1)
   		$this->assertEquals(1, sizeof($childPages))
    	$this->assertEquals(-2, $childPages[0]->getId())
   		$childPages = $this->_pc->getChildPages($p2)
   		$this->assertEquals(2, sizeof($childPages))
    	$this->assertEquals(-3, $childPages[0]->getId())
    	$this->assertEquals(-4, $childPages[1]->getId())
    

    public function testGenerateHierarchyWithAndWithoutFilename(
    
		$pc = $this->_pc
		$rootPage = $pc->getRootPage()
		$rootPage->generateHierarchy($pc, 'test1')
		$rootPage->generateHierarchy($pc, 'test1')
        $rootPage->generateHierarchy($pc)
        $rootPage->generateHierarchy($pc)
    

    public function testGetChildPageWithFilename(
    
		$pc = $this->_pc
		$rootPage = $pc->getRootPage()
		$test1 = $pc->getChildPage($rootPage, 'test1')
		$test2_1 = $pc->getChildPage($test1, 'test2')
		$test2_2 = $pc->getChildPage($rootPage, 'test2')
		$this->assertEquals(2, $test1->getId())
		$this->assertEquals(6, $test2_1->getId())
		$this->assertEquals(124, $test2_2->getId())
    


