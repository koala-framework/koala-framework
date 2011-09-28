<?php
/**
 * @group Component_CacheVars
 */
class Vps_Component_CacheVars_Menu_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_CacheVars_Menu_Root');
    }

    public function testMenu()
    {
        $cacheVars = $this->_root
            ->getComponent()->getCacheVars();
        $this->assertEquals(array(), $cacheVars);

        $menu = $this->_root->getChildComponent('-menu');
        $cacheVars = $menu->getComponent()->getStaticCacheVars();
        $this->assertEquals(4, count($cacheVars));
        $this->assertEquals('Vps_Component_CacheVars_Menu_PageModel', get_class($cacheVars[0]['model']));
        $this->assertEquals('Vps_Component_CacheVars_Menu_Model', get_class($cacheVars[1]['model']));
        $this->assertEquals('Vps_Component_Model', $cacheVars[2]['model']);
        $this->assertEquals(Vps_Registry::get('config')->user->model, $cacheVars[3]['model']);
    }
}
