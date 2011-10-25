<?php
/**
 * @group Update
 */
class Kwf_Update_SqlTest extends Kwf_Test_TestCase
{
    public function testSql()
    {
        $update = new Kwf_Update_Sql(123, 'abc');
        $tableName = uniqid("updatesql");
        $update->sql = "CREATE TABLE $tableName (
                            `id` INT NOT NULL ,
                            PRIMARY KEY ( `id` ) 
                            ) ENGINE = INNODB";
        $update->preUpdate();
        $update->update();
        $update->postUpdate();

        $tables = array();
        foreach (Kwf_Registry::get('db')->query("SHOW TABLES")->fetchAll() as $t) {
            reset($t);
            $tables[] = current($t);
        }
        $this->assertContains($tableName, $tables);

        Kwf_Registry::get('db')->query("DROP TABLE $tableName");
    }
}
