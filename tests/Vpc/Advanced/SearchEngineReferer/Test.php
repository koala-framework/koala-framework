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
        Vps_Component_ModelObserver::getInstance()->clear();
        Vps_Component_ModelObserver::getInstance()->setSkipFnF(true);
    }

    public function testCache()
    {
        $cacheVars = $this->_root
            ->getChildComponent('-referer2')
            ->getChildComponent('-view')->getComponent()->getCacheVars();
        $this->assertEquals(1, count($cacheVars));
        $this->assertEquals('Vpc_Advanced_SearchEngineReferer_Referer2_Model', get_class($cacheVars[0]['model']));
    }

    public function testComponentNewEntry()
    {
        $ref2 = $this->_root->getChildComponent('-referer2')->getComponent();
        $model = $ref2->getChildModel();
        $oldRow = $model->getRow($model->select()
            ->whereEquals('component_id', 'root')
            ->order('id', 'DESC')
        );
        $this->assertEquals($oldRow->id, 2);

        $_SERVER['HTTP_REFERER'] = 'http://www.google.at/search?hl=de&q=foo2';
        $ref2->processInput();
        $newRow = $model->getRow($model->select()
            ->whereEquals('component_id', 'root')
            ->order('id', 'DESC')
        );
        $this->assertEquals($oldRow->id, $newRow->id);

        $_SERVER['HTTP_REFERER'] = 'http://www.google.at/search?hl=de&q=fooNew';
        $ref2->processInput();
        $newRow = $model->getRow($model->select()
            ->whereEquals('component_id', 'root')
            ->order('id', 'DESC')
        );
        $this->assertEquals(6, $newRow->id);

        $_SERVER['HTTP_REFERER'] = 'http://www.google.at/search?hl=de&q=foo2';
        $ref2->processInput();
        $newRow = $model->getRow($model->select()
            ->whereEquals('component_id', 'root')
            ->order('id', 'DESC')
        );
        $this->assertEquals(7, $newRow->id);

        // Wenn &url= in Url vorkommt, nicht tracken (Adi fragen warum)
        $count = count($model->getRows());
        $_SERVER['HTTP_REFERER'] = 'http://www.google.at/search?hl=de&q=foo3&url=foo';
        $ref2->processInput();
        $this->assertEquals($count, count($model->getRows()));
    }
}
