<?php
class Kwf_Util_MemcacheSessionHandler_Test extends Kwf_Test_TestCase
{
    public function setUp()
    {
        Kwf_Test_SeparateDb::createSeparateTestDb(dirname(__FILE__).'/bootstrap.sql');
        Kwf_Cache::factory('Core', 'Memcached', array(
            'lifetime'=>null,
            'automatic_cleaning_factor' => false,
            'automatic_serialization'=>true))->clean();
        ini_set("session.gc_maxlifetime", 2); //two seconds
    }

    public function tearDown()
    {
        ini_restore("session.gc_maxlifetime");
        Kwf_Test_SeparateDb::restoreTestDb();
    }

    public function testStandardReadWrite()
    {
        $h = new Kwf_Util_SessionHandler();
        $h->open('', '');
        $this->assertEquals($h->read('123456789'), ''); //initial empty
        $h->write('123456789', 'asdf');
        $h->close();


        $h->open('', '');
        $this->assertEquals($h->read('123456789'), 'asdf');
        $h->write('123456789', 'asdf1');
        $h->close();

        $h->open('', '');
        $this->assertEquals($h->read('123456789'), 'asdf1');
    }

    /**
     * @group slow
     */
    public function testExpire()
    {
        $h = new Kwf_Util_SessionHandler();
        $h->open('', '');
        $this->assertEquals($h->read('123456789'), ''); //initial empty
        $h->write('123456789', 'asdf');
        $h->close();

        sleep(3);
        $h->gc(2);

        $h->open('', '');
        $this->assertEquals($h->read('123456789'), ''); //expired, must be empty
        $h->close();
    }

    public function testMemcacheCleared()
    {
        $h = new Kwf_Util_SessionHandler();
        $h->open('', '');
        $h->write('123456789', 'asdf');
        $h->close();

        Kwf_Cache::factory('Core', 'Memcached', array(
            'lifetime'=>null,
            'automatic_cleaning_factor' => false,
            'automatic_serialization'=>true))->clean();


        $h->open('', '');
        $this->assertEquals($h->read('123456789'), 'asdf');
        $h->close();
    }

    public function testConcurentWrite()
    {
        $h = new Kwf_Util_SessionHandler();
        $h2 = new Kwf_Util_SessionHandler();
        $h->open('', '');
        $h2->open('', '');

        $h->read('123456789');
        $h->write('123456789', 'asdf');

        $h2->read('123456789');
        $h->read('123456789');
        $h2->write('123456789', 'asdf1');
        $h->write('123456789', 'asdf');
        $this->assertEquals($h->read('123456789'), 'asdf1');

        $h->close();
        $h2->close();
    }

    /**
     * @group slow
     */
    public function testDontExpireWithoutChanges()
    {
        $h = new Kwf_Util_MemcacheSessionHandler_TestSessionHandler(array(
            'lifeTime' => 3,
            'refreshTime' => 2,
        ));
        $h->open('', '');
        $h->write('123456789', 'asdf');
        $h->close();

        for($i=0;$i<3;$i++) {
            //read + write without actually changing anything
            sleep(1);
            $h->open('', '');
            $this->assertEquals($h->read('123456789'), 'asdf');
            $h->write('123456789', 'asdf');
            $h->close();
            $h->gc(2);
        }

        $h->open('', '');
        $this->assertEquals($h->read('123456789'), 'asdf');
        $h->close();
    }

    /**
     * @group slow
     */
    public function testExpireWithoutChanges()
    {
        $lifeTime = 4;
        $h = new Kwf_Util_MemcacheSessionHandler_TestSessionHandler(array(
            'lifeTime' => $lifeTime,
            'refreshTime' => 2,
        ));
        $h->open('', '');
        $h->write('123456789', 'asdf');
        $h->close();

        for($i=0;$i<2;$i++) {
            //read + write without actually changing anything
            sleep(1);
            $h->open('', '');
            $this->assertEquals($h->read('123456789'), 'asdf');
            $h->write('123456789', 'asdf');
            $h->close();
            $h->gc($lifeTime);
        }
        sleep(3);

        $h->gc($lifeTime);

        $h->open('', '');
        $this->assertEquals($h->read('123456789'), '');
        $h->close();
    }
}
