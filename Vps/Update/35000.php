<?php
class Vps_Update_35000 extends Vps_Update
{
    public function update()
    {
        parent::update();

        $db = Vps_Registry::get('db');

        $fields = array();

        $dir = new DirectoryIterator('application/models');
        foreach ($dir as $file) {
            if ($file->isDir() || $file->isDot()) continue;
            $name = substr($file, 0, -4);
            if ($name != '.direc' && is_instance_of($name, 'Vps_Model_Abstract')) {
                try {
                    $model = Vps_Model_Abstract::getInstance($name);
                    $fields = array_merge($fields, $this->_getFieldModelData($model));
                } catch (Exception $e) {
                    echo "Model $name konnte nicht auf Vps_Model_Field geprueft werden.";
                }
            }
        }

        foreach (Vpc_Abstract::getComponentClasses() as $class) {
            if (is_instance_of($class, 'Vpc_Basic_Text_Component')) {
                $model = Vpc_Basic_Text_Component::getTextModel($class);
            } else {
                $model = Vpc_Abstract::createOwnModel($class);
            }
            if ($model) {
                $fields = array_merge($fields, $this->_getFieldModelData($model));
            }

            $model = Vpc_Abstract::createChildModel($class);
            if ($model) {
                $fields = array_merge($fields, $this->_getFieldModelData($model));
            }

            $model = Vpc_Abstract::createFormModel($class);
            if ($model) {
                $fields = array_merge($fields, $this->_getFieldModelData($model));
            }
        }

        $db->query("SET NAMES utf8");
        foreach ($fields as $tablename => $fieldnames) {
            // checken, ob das feld wirklich in der datenbank-tabelle existiert
            // wenn das entsprechende feld nicht existiert, würde sonst ein fehler auftreten.
            // das hat folgenden hintergrund:
            // wenn eine komponente mit einem update-script >35000 ein 'data' field
            // anlegt, dann will er das hier durchlaufen und updaten, weils ja im
            // php-code schon als sibling angegeben ist. und das problem ist dann,
            // dass das echte DB-Feld erst mit dem script >35000 angelegt wird.
            // wenn so ein fall auftritt (zB bei der Tabelle vpc_composite_list)
            // dann ignorieren wir das updaten dieses feldes einfach weil sowieso
            // noch nix drinsteht wenns erst später angelegt wird.
            $reallyExistingFieldsInTable = array();
            foreach ($db->query("SHOW COLUMNS FROM {$tablename}")->fetchAll() as $reallyFieldRow) {
                $reallyExistingFieldsInTable[] = $reallyFieldRow['Field'];
            }

            foreach ($fieldnames as $fieldname) {
                if (!in_array($fieldname, $reallyExistingFieldsInTable)) continue; // siehe fetter kommentar paar zeilen drüber

                //$sql = "UPDATE $tablename SET $fieldname=REPLACE($fieldname, 'u00', '\\\\u00')";
                foreach ($db->fetchCol("SELECT DISTINCT $fieldname FROM $tablename") as $oldval) {
                    if ($oldval == '') continue;
                    $val = @unserialize($oldval);
                    if ($val === false) continue;
                    $val = json_encode($val);
                    $val = str_replace('\u', '\\\\u', $val);
                    $sql = "UPDATE $tablename SET $fieldname='$val' WHERE $fieldname='$oldval'";
                    //p($sql);
                    $db->query($sql);
                }
            }
        }
    }

    private function _getFieldModelData(Vps_Model_Abstract $model)
    {
        $ret = array();
        if (!$model instanceof Vps_Model_Db && !$model instanceof Vps_Model_Db_Proxy) return $ret;
        $tablename = $model instanceof Vps_Model_Db_Proxy ?
            $model->getProxyModel()->getTablename() :
            $model->getTablename();
        foreach ($model->getSiblingModels() as $field => $m) {
            if ($m instanceof Vps_Model_Field) {
                $ret[$tablename][] = $m->getFieldName();
            }
        }
        return $ret;
    }
}
