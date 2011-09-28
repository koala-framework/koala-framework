<?php
/**
 * @group slow
 * @group Model
 * @group User
 * @group Model_User
 * @group Real_Model_User
 */
class Vps_User_RealModelTest extends Vps_Test_TestCase
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
        return 'vpstest'.time().'_'.(self::$_lastMailNumber).'@vivid.vps';
    }

    /**
     * @expectedException Vps_Exception
     */
    public function testCreateRow()
    {
        $m = new Vps_User_Model();
        $r = $m->createRow();
    }

    public function testWithWebcode()
    {
        // create -> lock -> unlock -> delete -> create

        $webId = Vps_Registry::get('config')->application->id;
        $webcode = Vps_Registry::get('config')->service->users->webcode;
        if (!$webcode) {
            $this->markTestSkipped();
        }

        $email = $this->_getNewMailAddress();

        // CREATE USER \\
        $m = new Vps_User_Model();
        $r = $m->createUserRow($email);
        $r->gender = 'male';
        $r->title = 'Dr.';
        $r->firstname = 'Test';
        $r->lastname = 'Testermann';
        $r->save();

        // testing service users table
        $all = new Vps_User_All_Model();
        $allr = $all->getRow($all->select()->whereEquals('id', $r->id)->order('id', 'DESC'));
        $this->assertEquals($allr->id, $r->id);
        $this->assertEquals($email, $allr->email);

        // testing service relation table
        $rel = new Vps_User_Relation_Model();
        $relr = $rel->getRow($rel->select()->whereEquals('user_id', $r->id)->order('id', 'DESC'));
        $this->assertEquals($r->id, $relr->user_id);
        $this->assertEquals($webId, $relr->web_id);
        $this->assertEquals(0, $relr->locked);
        $this->assertEquals(0, $relr->deleted);

        // testing the mirror in web
        $mir = new Vps_User_Mirror();
        $mirr = $mir->getRow($mir->select()->whereEquals('id', $r->id)->order('id', 'DESC'));
        $this->assertEquals($r->id, $mirr->id);
        $this->assertEquals($email, $mirr->email);
        $this->assertEquals($webcode, $mirr->webcode);
        $this->assertEquals(0, $mirr->locked);
        $this->assertEquals(0, $mirr->deleted);
        $this->assertEquals('Testermann', $mirr->lastname);

        // testing web model
        $web = new Vps_User_Web_Model();
        $webr = $web->getRow($web->select()->whereEquals('id', $r->id)->order('id', 'DESC'));
        $this->assertEquals($r->id, $webr->id);
        $this->assertEquals('guest', $webr->role);

        // LOCK USER \\
        $r->locked = 1;
        $r->save();

        // testing service relation table
        $rel = new Vps_User_Relation_Model();
        $relr = $rel->getRow($rel->select()->whereEquals('user_id', $r->id)->whereEquals('web_id', $webId));
        $this->assertEquals(1, $relr->locked);

        // testing the mirror in web
        $mir = new Vps_User_Mirror();
        $mirr = $mir->getRow($r->id);
        $this->assertEquals(1, $mirr->locked);

        // UNLOCK USER \\
        $r->locked = 0;
        $r->save();

        // testing service relation table
        $rel = new Vps_User_Relation_Model();
        $relr = $rel->getRow($rel->select()->whereEquals('user_id', $r->id)->whereEquals('web_id', $webId));
        $this->assertEquals(0, $relr->locked);

        // testing the mirror in web
        $mir = new Vps_User_Mirror();
        $mirr = $mir->getRow($r->id);
        $this->assertEquals(0, $mirr->locked);

        // DELETE USER \\
        $r->deleted = 1;
        $r->save();

        // testing service relation table
        $rel = new Vps_User_Relation_Model();
        $relr = $rel->getRow($rel->select()->whereEquals('user_id', $r->id)->whereEquals('web_id', $webId));
        $this->assertEquals(1, $relr->deleted);

        // testing the mirror in web
        $mir = new Vps_User_Mirror();
        $mirr = $mir->getRow($r->id);
        $this->assertEquals(1, $mirr->deleted);

        // CREATE USER \\
        $m2 = new Vps_User_Model();
        $r2 = $m2->createUserRow($email);
        $r2->gender = 'male';
        $r2->title = 'Dr. 2';
        $r2->firstname = 'Test 2';
        $r2->lastname = 'Testermann 2';
        $r2->save();

        // testing service users table
        $all = new Vps_User_All_Model();
        $allr = $all->getRow($all->select()->whereEquals('id', $r2->id)->order('id', 'DESC'));
        $this->assertEquals($allr->id, $r2->id);
        $this->assertNotEquals($allr->id, $r->id); // muss eine andere row wie die ganz oben sein
        $this->assertEquals($email, $allr->email);
        $this->assertEquals('Test 2', $allr->firstname);

        // testing service relation table
        $rel = new Vps_User_Relation_Model();
        $relr = $rel->getRow($rel->select()->whereEquals('user_id', $r2->id)->order('id', 'DESC'));
        $this->assertEquals($r2->id, $relr->user_id);
        $this->assertEquals($webId, $relr->web_id);
        $this->assertEquals(0, $relr->locked);
        $this->assertEquals(0, $relr->deleted);

        // testing the mirror in web
        $mir = new Vps_User_Mirror();
        $mirr = $mir->getRow($mir->select()->whereEquals('id', $r2->id)->order('id', 'DESC'));
        $this->assertEquals($r2->id, $mirr->id);
        $this->assertNotEquals($r->id, $mirr->id);
        $this->assertEquals($email, $mirr->email);
        $this->assertEquals($webcode, $mirr->webcode);
        $this->assertEquals(0, $mirr->locked);
        $this->assertEquals(0, $mirr->deleted);
        $this->assertEquals('Testermann 2', $mirr->lastname);

        // testing web model
        $web = new Vps_User_Web_Model();
        $webr = $web->getRow($web->select()->whereEquals('id', $r2->id)->order('id', 'DESC'));
        $this->assertEquals($r2->id, $webr->id);
        $this->assertNotEquals($r->id, $webr->id);
        $this->assertEquals('guest', $webr->role);
    }

    public function testWithoutWebcode()
    {
        // create -> lock -> unlock -> delete -> create

        $webId = Vps_Registry::get('config')->application->id;
        $webcode = '';

        $email = $this->_getNewMailAddress();

        // CREATE USER \\
        $m = new Vps_User_Model();
        $r = $m->createUserRow($email, $webcode);
        $r->gender = 'male';
        $r->title = 'Dr.';
        $r->firstname = 'Test Global';
        $r->lastname = 'Testermann Global';
        $r->save();

        // testing service users table
        $all = new Vps_User_All_Model();
        $allr = $all->getRow($all->select()->whereEquals('id', $r->id)->order('id', 'DESC'));
        $this->assertEquals($allr->id, $r->id);
        $this->assertEquals($email, $allr->email);

        // testing service relation table
        $rel = new Vps_User_Relation_Model();
        $relr = $rel->getRow($rel->select()->whereEquals('user_id', $r->id)->order('id', 'DESC'));
        $this->assertEquals($r->id, $relr->user_id);
        $this->assertEquals($webId, $relr->web_id);
        $this->assertEquals(0, $relr->locked);
        $this->assertEquals(0, $relr->deleted);

        // testing the mirror in web
        $mir = new Vps_User_Mirror();
        $mirr = $mir->getRow($mir->select()->whereEquals('id', $r->id)->order('id', 'DESC'));
        $this->assertEquals($r->id, $mirr->id);
        $this->assertEquals($email, $mirr->email);
        $this->assertEquals($webcode, $mirr->webcode);
        $this->assertEquals(0, $mirr->locked);
        $this->assertEquals(0, $mirr->deleted);
        $this->assertEquals('Testermann Global', $mirr->lastname);

        // testing web model
        $web = new Vps_User_Web_Model();
        $webr = $web->getRow($web->select()
            ->whereEquals('id', $r->id)
            ->order('id', 'DESC')
        );
        $this->assertEquals($r->id, $webr->id);
        $this->assertEquals('guest', $webr->role);

        // switch role to 'user'
        $r->role = 'user';
        $r->save();

        $web = new Vps_User_Web_Model();
        $webr = $web->getRow($web->select()
            ->whereEquals('id', $r->id)
            ->order('id', 'DESC')
        );
        $this->assertEquals($r->id, $webr->id);
        $this->assertEquals('user', $webr->role);

        // LOCK USER \\
        $r->locked = 1;
        $r->save();

        // testing service relation table
        $rel = new Vps_User_Relation_Model();
        $relr = $rel->getRow($rel->select()->whereEquals('user_id', $r->id)->whereEquals('web_id', $webId));
        $this->assertEquals(1, $relr->locked);

        // testing the mirror in web
        $mir = new Vps_User_Mirror();
        $mirr = $mir->getRow($r->id);
        $this->assertEquals(1, $mirr->locked);

        // UNLOCK USER \\
        $r->locked = 0;
        $r->save();

        // testing service relation table
        $rel = new Vps_User_Relation_Model();
        $relr = $rel->getRow($rel->select()->whereEquals('user_id', $r->id)->whereEquals('web_id', $webId));
        $this->assertEquals(0, $relr->locked);

        // testing the mirror in web
        $mir = new Vps_User_Mirror();
        $mirr = $mir->getRow($r->id);
        $this->assertEquals(0, $mirr->locked);

        // DELETE USER \\
        $r->deleted = 1;
        $r->save();

        // testing service relation table
        $rel = new Vps_User_Relation_Model();
        $relr = $rel->getRow($rel->select()->whereEquals('user_id', $r->id)->whereEquals('web_id', $webId));
        $this->assertEquals(1, $relr->deleted);

        // testing the mirror in web
        $mir = new Vps_User_Mirror();
        $mirr = $mir->getRow($r->id);
        $this->assertEquals(1, $mirr->deleted);

        // CREATE USER \\
        $m2 = new Vps_User_Model();
        $r2 = $m2->createUserRow($email, $webcode);
        $r2->title = 'Dr. 2';
        $r2->firstname = 'Test Global 2';
        $r2->lastname = 'Testermann Global 2';
        $r2->deleted = 0;
        $r2->save();

        $this->assertEquals($r->id, $r2->id); // muss die gleiche row wie die ganz oben sein

        // testing service users table
        $all = new Vps_User_All_Model();
        $allr = $all->getRow($all->select()
            ->whereEquals('email', $r2->email)
            ->order('id', 'DESC')
        );
        $this->assertEquals($allr->id, $r2->id);
        $this->assertEquals('Test Global 2', $allr->firstname);

        // testing service relation table
        $rel = new Vps_User_Relation_Model();
        $relr = $rel->getRow($rel->select()
            ->whereEquals('user_id', $r2->id)
            ->order('id', 'DESC')
        );
        $this->assertEquals($r2->id, $relr->user_id);
        $this->assertEquals($webId, $relr->web_id);
        $this->assertEquals(0, $relr->locked);
        $this->assertEquals(0, $relr->deleted);

        // testing the mirror in web
        $mir = new Vps_User_Mirror();
        $mirr = $mir->getRow($mir->select()
            ->whereEquals('email', $r2->email)
            ->order('id', 'DESC')
        );
        $this->assertEquals($r2->id, $mirr->id);
        $this->assertEquals($email, $mirr->email);
        $this->assertEquals($webcode, $mirr->webcode);
        $this->assertEquals(0, $mirr->locked);
        $this->assertEquals(0, $mirr->deleted);
        $this->assertEquals('Testermann Global 2', $mirr->lastname);

        // testing web model
        $web = new Vps_User_Web_Model();
        $webr = $web->getRow($web->select()
            ->whereEquals('id', $r2->id)
            ->order('id', 'DESC')
        );
        $this->assertEquals($r2->id, $webr->id);
        $this->assertEquals('guest', $webr->role);
    }

    public function testCreateUserRowGlobalOnly()
    {
        // Annahme: user gibt es nur global, im web noch nicht.
        // nach createUserRow müsste es ihn dann im web auch sofort geben
        // und man muss die row erhalten

        $webId = Vps_Registry::get('config')->application->id;
        $webcode = '';

        $email = $this->_getNewMailAddress();

        $m = new Vps_User_Model();
        $all = new Vps_User_All_Model();

        $newestAllRow = $all->getRow($all->select()->order('id', 'DESC'));
        $newestId = $newestAllRow->id;

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
            'created' => date('Y-m-d H:i:s', time() - 10),
            'last_modified' => date('Y-m-d H:i:s', time() - 10)
        ));
        $allr->save();
        $this->assertGreaterThan($newestAllRow->id, $allr->id);

        // user im web suchen, sollte nicht vorhanden sein
        $r = $m->getRow($m->select()->whereEquals('email', $email));
        $this->assertNull($r);

        // createUserRow in usermodel aufrufen, sollte den user im web anlegen, auch ohne speichern
        $r = $m->createUserRow($email, $webcode);

        $r = $m->getRow($m->select()->whereEquals('email', $email));
        $this->assertNotNull($r);
        $this->assertEquals($allr->id, $r->id);
        $this->assertEquals('m', $r->firstname);

        $r->gender = 'male';
        $r->title = 'Dr.';
        $r->firstname = 'Test Global';
        $r->lastname = 'Testermann Global';
        $r->save();

        $r = $m->getRow($m->select()->whereEquals('email', $email));
        $this->assertEquals($allr->id, $r->id);
        $this->assertEquals('Dr.', $r->title);
        $this->assertEquals('Test Global', $r->firstname);
        $this->assertEquals('Testermann Global', $r->lastname);
    }

    public function testCreateUserRowDeleted()
    {
        // Annahme: globalen user gibt es im web, aber er hat das deleted flag gesetzt
        // nach createUserRow müsste das deleted flag auf 0 sein
        // und man muss die row erhalten

        $webId = Vps_Registry::get('config')->application->id;
        $webcode = '';

        $email = $this->_getNewMailAddress();

        $m = new Vps_User_Model();

        $r = $m->createUserRow($email, $webcode);
        $r->gender = 'male';
        $r->title = 'Dr.';
        $r->firstname = 'mia';
        $r->lastname = 'Testermann Global';
        $r->save();

        $r->deleted = 1;
        $r->save();

        $this->assertNotNull($r);
        $this->assertEquals(1, $r->deleted);

        // createUserRow in usermodel aufrufen
        // sollte den gelöschten user zurückgeben, ohne deleted flag
        $createRow = $m->createUserRow($email, $webcode);

        $this->assertNotNull($createRow);
        // die createRow soll die gleiche id haben wie die zuvor angelegte row
        $this->assertEquals($r->id, $createRow->id);
        $this->assertEquals('mia', $createRow->firstname);
        $this->assertEquals(0, $createRow->deleted);
        $this->assertEquals(0, $createRow->locked);

        $createRow->firstname = 'mia Global';
        $createRow->save();

        $this->assertEquals('mia Global', $createRow->firstname);

        $newRow = $m->getRow($m->select()->whereEquals('email', $email));

        $this->assertNotNull($newRow);
        $this->assertEquals('mia Global', $newRow->firstname);
        $this->assertEquals(0, $newRow->deleted);
        $this->assertEquals(0, $newRow->locked);
    }

    /**
     * @expectedException Vps_ClientException
     */
    public function testCreateUserRowExisting()
    {
        // Annahme: globalen user gibt es im web und ist aktiv
        // createUserRow müsste eine ClientException werfen, dass User schon existent ist

        $webId = Vps_Registry::get('config')->application->id;
        $webcode = '';

        $email = $this->_getNewMailAddress();

        $m = new Vps_User_Model();

        $r = $m->createUserRow($email, $webcode);
        $r->gender = 'male';
        $r->title = 'Dr.';
        $r->firstname = 'moep';
        $r->lastname = 'blubb';
        $r->save();

        $this->assertNotNull($r);
        $this->assertEquals(0, $r->deleted);
        $this->assertEquals(0, $r->locked);

        // createUserRow in usermodel aufrufen
        // sollte eine exception werfen, dass user schon existent ist
        $createRow = $m->createUserRow($email, $webcode);
    }
}
