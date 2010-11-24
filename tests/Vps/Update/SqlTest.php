<?php
/**
 * @group Update
 */
class Vps_Update_SqlTest extends Vps_Test_TestCase
{
    public function testSql()
    {
        $this->markTestIncomplete();
        $update = new Vps_Update_Sql(123, 'abc');
        $tableName = uniqid("updatesql");
        $update->sql = "CREATE TABLE $tableName (
                            `id` INT NOT NULL ,
                            PRIMARY KEY ( `id` ) 
                            ) ENGINE = INNODB";
        $update->preUpdate();
        $update->update();
        $update->postUpdate();

        $tables = array();
        foreach (Vps_Registry::get('db')->query("SHOW TABLES")->fetchAll() as $t) {
            reset($t);
            $tables[] = current($t);
        }
        $this->assertContains($tableName, $tables);

        Vps_Registry::get('db')->query("DROP TABLE $tableName");
    }
}
