<?php
/**
 * @group Model
 * @group User
 * @group Model_User
 */
class Vps_User_RealModelTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Vps_Registry::set('db', Vps_Registry::get('dao')->getDb());
    }

    public function tearDown()
    {
        Vps_Registry::set('db', Vps_Test::getTestDb());
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
        if (!$webcode) return; // Dieser Test ist nur für webcode

        $email = 'vpstest'.time().mt_rand(0,99).'@vivid.vps';

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

        $email = 'vpstest'.time().mt_rand(0,99).'@vivid.vps';

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
}
