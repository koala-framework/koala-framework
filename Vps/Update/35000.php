<?php
class Vps_Update_35000 extends Vps_Update
{
    protected function _init()
    {
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

        foreach ($fields as $tablename => $fieldnames) {
            foreach ($fieldnames as $fieldname) {
                foreach ($db->fetchCol("SELECT DISTINCT $fieldname FROM $tablename") as $oldval) {
                    if ($oldval == '') continue;
                    $val = @unserialize($oldval);
                    if (!$val) continue;
                    $val = json_encode($val);
                    $sql = "UPDATE $tablename SET $fieldname='$val' WHERE $fieldname='$oldval'";
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
