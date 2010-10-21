<?php
/**
 * @group slow
 * @group Model
 * @group User
 * @group Model_User
 * @group Real_Model_User
 */
class Vps_User_SameDateTest extends PHPUnit_Framework_TestCase
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

    public function testCreateUserSameDateGlobal()
    {
        // es wäre möglich, dass zwei user in der selben sekunde angelegt werden
        // aber dann nur der erste gesynct wird, weil das web glaubt am aktuellen
        // stand zu sein, da der neueste user im web das gleiche datum wie der
        // neueste im Service hat. Dies wird hier getestet und wurde im service
        // durch den Filter 'Vps_Filter_Row_CurrentDateTimeUnique' behoben

        $webId = Vps_Registry::get('config')->application->id;
        $webcode = '';

        $m = new Vps_User_Model();
        $all = new Vps_User_All_Model();

        $mailAddresses = array();

        for ($i=0; $i<10; $i++) {
            $email = $this->_getNewMailAddress();
            $mailAddresses[] = $email;

            // globalen user erstellen, nicht in web
            $allr = $all->createRow(array(
                'email' => $email,
                'password' => '',
                'password_salt' => 'abcdefg',
                'gender' => 'male',
                'title' => '',
                'firstname' => 'm',
                'lastname' => 'h',
                'webcode' => '',
                'created' => date('Y-m-d H:i:s', time())
            ));
            $allr->save();
        }

        // alle diese user im web erstellen
        $lastModifiedDates = array();
        foreach ($mailAddresses as $ma) {
            $r = $m->createUserRow($ma, $webcode);
            if (in_array($r->last_modified, $lastModifiedDates)) {
                $this->fail("last_modified must be unique");
            }
            $lastModifiedDates[] = $r->last_modified;
        }
    }

    public function testCreateUserSameDateWeb()
    {
        $webId = Vps_Registry::get('config')->application->id;
        $webcode = Vps_Registry::get('config')->service->users->webcode;

        $m = new Vps_User_Model();

        $mailAddresses = array();
        $timeBefore = $timeAfter = array();

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

            if (in_array($r->last_modified, $lastModifiedDates)) {
                $this->fail("last_modified must be unique");
            }
            $lastModifiedDates[] = $r->last_modified;
        }
    }
}
