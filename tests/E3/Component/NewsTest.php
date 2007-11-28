<?php
class E3_Component_NewsTest extends E3_Test
{
    private $_dao;
    private $_pc;

    public function setUp()
    {
        $this->_dao = $this->createDao();
        $this->_pc = new E3_PageCollection_Tree($this->_dao);
    }
    public function testNews()
    {
        $news = new E3_Component_News_Aktuelle($this->_dao, 7);
        $this->_pc->addPage($news, 'news');
        $news->generateHierarchy($this->_pc);

        $detailsPages = $this->_pc->getChildPages($news);

        $this->assertEquals('7_1', $detailsPages[0]->getId());
        $this->assertEquals('7_2', $detailsPages[1]->getId());
        
        $vars = $detailsPages[0]->getTemplateVars();
        $this->assertEquals('News/Details.html', $vars['template']);
        $this->assertEquals('8', $vars['content']['id']);
        $this->assertEquals('Textbox.html', $vars['content']['template']);
    }
}
