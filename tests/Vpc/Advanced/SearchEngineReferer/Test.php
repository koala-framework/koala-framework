<?php
/**
 * @group Vpc_Advanced_Referer
 */
class Vpc_Advanced_SearchEngineReferer_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Advanced_SearchEngineReferer_Root');
    }

    public function tearDown()
    {
        if (isset($_SERVER['HTTP_REFERER'])) unset($_SERVER['HTTP_REFERER']);
        parent::tearDown();
    }

    public function testComponentNewEntry()
    {
        $ref2 = $this->_root->getChildComponent('-referer2')->getComponent();
        $model = $ref2->getChildModel();

        $oldRow = $model->getRow($model->select()
            ->whereEquals('component_id', 'root-referer2')
            ->order('id', 'DESC')
        );
        $this->assertEquals($oldRow->id, 2);

        $_SERVER['HTTP_REFERER'] = 'http://www.google.at/search?hl=de&q=foo2';
        $ref2->processInput();
        $newRow = $model->getRow($model->select()
            ->whereEquals('component_id', 'root-referer2')
            ->order('id', 'DESC')
        );
        $this->assertEquals($oldRow->id, $newRow->id);

        $render = $ref2->getData()->render();
        $_SERVER['HTTP_REFERER'] = 'http://www.google.at/search?hl=de&q=fooNew';
        $ref2->processInput();
        $newRow = $model->getRow($model->select()
            ->whereEquals('component_id', 'root-referer2')
            ->order('id', 'DESC')
        );
        $this->assertEquals(6, $newRow->id);

        $_SERVER['HTTP_REFERER'] = 'http://www.google.at/search?hl=de&q=foo2';
        $ref2->processInput();
        $newRow = $model->getRow($model->select()
            ->whereEquals('component_id', 'root-referer2')
            ->order('id', 'DESC')
        );
        $this->assertEquals(7, $newRow->id);

        // Wenn &url= in Url vorkommt, nicht tracken (Adi fragen warum)
        $count = count($model->getRows());
        $_SERVER['HTTP_REFERER'] = 'http://www.google.at/search?hl=de&q=foo3&url=foo';
        $ref2->processInput();
        $this->assertEquals($count, count($model->getRows()));
    }

    public function testCache()
    {
        $ref2 = $this->_root->getChildComponent('-referer2');
        $ref2->getChildComponent('-view')->getComponent()->emptyReferersCache();
        $render = $ref2->render();
        $this->assertEquals(3, substr_count($render, 'foo1'));
        $this->assertEquals(3, substr_count($render, 'foo2'));
        $this->assertEquals(2, substr_count($render, '<li'));

        $_SERVER['HTTP_REFERER'] = 'http://www.google.at/search?hl=de&q=fooNew';
        $ref2 = $this->_root->getChildComponent('-referer2');
        $ref2->getComponent()->processInput();
        $ref2->getChildComponent('-view')->getComponent()->emptyReferersCache();
        $render = $ref2->render();
        $this->assertEquals(3, substr_count($render, 'fooNew'));
        $this->assertEquals(3, substr_count($render, 'foo1'));
        $this->assertEquals(3, substr_count($render, 'foo2'));
        $this->assertEquals(3, substr_count($render, '<li'));
    }
}
