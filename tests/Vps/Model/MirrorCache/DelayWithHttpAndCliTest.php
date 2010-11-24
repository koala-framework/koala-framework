<?php
/**
 * Testet ob cli und http zugriffe eh auch nur alle x minuten syncen
 * es gab ein problem mit berechtigungen
 * @group Model_MirrorCache
 * @group Model_MirrorCache_Delay
 * @group slow
 */
class Vps_Model_MirrorCache_DelayWithHttpAndCliTest extends Vps_Test_TestCase
{
    public function testSyncWithCli()
    {
        $d = Zend_Registry::get('testDomain');
        if (substr($d, -6) != '.vivid' && substr($d, -18) != '.vivid-test-server') {
            //online gibts keine test-datenbank
            $this->markTestSkipped();
        }

        Vps_Benchmark::enable();
        Vps_Benchmark::reset();

        Vps_Model_MirrorCache_TestController::setup();
        Vps_Model_MirrorCache_TestController::$proxyModel->synchronize(Vps_Model_MirrorCache::SYNC_ONCE);
        $this->assertEquals(1, (int)Vps_Benchmark::getCounterValue('mirror sync'));

        $url = 'http://'.Vps_Registry::get('testDomain').'/vps/test/vps_model_mirror-cache_test';
        $this->assertEquals(0, file_get_contents($url));

        sleep(6); //sync delay ist 5 sec
        $this->assertEquals(1, file_get_contents($url));
        $this->assertEquals(0, file_get_contents($url));

        Vps_Benchmark::reset();
        Vps_Model_MirrorCache_TestController::$proxyModel->countRows();
        $this->assertEquals(0, (int)Vps_Benchmark::getCounterValue('mirror sync'));

        sleep(6); //sync delay ist 5 sec
        Vps_Benchmark::reset();
        Vps_Model_MirrorCache_TestController::$proxyModel->synchronize(Vps_Model_MirrorCache::SYNC_ALWAYS);
        $this->assertEquals(1, (int)Vps_Benchmark::getCounterValue('mirror sync'));
        $this->assertEquals(0, file_get_contents($url));
    }
}
