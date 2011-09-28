<?php
/**
 * @group slow
 * @group Model
 * @group User
 * @group Model_User
 * @group Real_Model_User
 */
class Vps_User_SameDateTest extends Vps_Test_TestCase
{
    private static $_lastMailNumber = 0;

    public function setUp()
    {
        parent::setUp();
        Vps_Test_SeparateDb::createSeparateTestDb(dirname(__FILE__).'/bootstrap.sql');
    }

    public function tearDown()
    {
        $this->assertFalse(Vps_User_Model::isLockedCreateUser());
        Vps_Test_SeparateDb::restoreTestDb();
        parent::tearDown();
    }

    private function _getNewMailAddress()
    {
        self::$_lastMailNumber += 1;
        return 'vpstestsu'.time().'_'.(self::$_lastMailNumber).'@vivid.vps';
    }

    public function testCreateUserSameDate()
    {
        $webId = Vps_Registry::get('config')->application->id;
        $webcode = Vps_Registry::get('config')->service->users->webcode;
        if (!$webcode) {
            $this->markTestSkipped();
        }

        $m = new Vps_User_Model();
        $mailAddresses = array();
        $timeBefore = $timeAfter = array();
        $rows = array();

        $lastModifiedDates = array();
        for ($i=0; $i<10; $i++) {
            $email = $this->_getNewMailAddress();
            $mailAddresses[] = $email;

            $r = $m->createUserRow($email, $webcode);
            $r->gender = 'male';
            $r->title = '';
            $r->firstname = 'first '.$i;
            $r->lastname = 'last '.$i;
            $r->created = date('Y-m-d H:i:s', time());
            $r->save();

            $rows[] = $r;
            $lastModifiedDates[] = $r->last_modified;
        }

        // aus web alle user holen ob sie korrekt gesynct sind
        if (!count($mailAddresses)) {
            $this->fail("no users created");
        }

        $db = Vps_Registry::get('db');
        $users = $db->query("SELECT * FROM cache_users WHERE email IN('".implode("','", $mailAddresses)."')")->fetchAll();
        if (count($users) != 10) {
            $this->fail("exactly 10 users must be created and synced");
        }

        sleep(2);
        // Step 1: user normal über model ändern
        // Step 2: anderen user direkt im service ändern
        // Step 3: check, ob der sync inkorrekt ist (soll so sein)
        // Step 4: syncen
        // Step 5: in service geänderter muss auch in web korrekt da sein
        $allModel = new Vps_User_All_Model();
        $allRow2 = $allModel->getRow($rows[9]->id);
        $r1 = $rows[0];
        $i = 1;
        while ($i==1 || $r1->last_modified != $allRow2->last_modified) {
            $allRow2->title = 'Mag.'.($i);

            $r1->title = 'Dr.'.($i++);
            $r1->save(); // Step 1

            $allRow2->save(); // Step 2

            if ($i >= 10) {
                $this->fail("No same date within 10 tries...damn test");
                break;
            }
        }

        // Step 3
        $m = new Vps_User_Model();
        $checkRow = $m->getRow($allRow2->id);
        if ($checkRow->last_modified == $allRow2->last_modified) {
            $this->fail("last_modified should not be the same here. possible bad written test.");
        }

        // Step 4
        $m = new Vps_User_Model();
        $m->synchronize(Vps_Model_MirrorCache::SYNC_ALWAYS);

        // Step 5
        $checkRow = $m->getRow($allRow2->id);
        if ($checkRow->last_modified != $allRow2->last_modified) {
            $this->fail("Web is not in sync after synchronize-call. last_modified Web: {$checkRow->last_modified} Service: {$allRow2->last_modified}");
        }

    }
}
