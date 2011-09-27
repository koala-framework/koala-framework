<?php
class Vpc_Abstract_Admin extends Vps_Component_Abstract_Admin
{
    protected function _getRow($componentId)
    {
        if (!Vpc_Abstract::hasSetting($this->_class, 'tablename')) return null;
        $tablename = Vpc_Abstract::getSetting($this->_class, 'tablename');
        if ($tablename) {
            $table = new $tablename(array('componentClass'=>$this->_class));
            return $table->find($componentId)->current();
        }
        return null;
    }

    protected function _getRows($componentId)
    {
        $tablename = Vpc_Abstract::getSetting($this->_class, 'tablename');
        if ($tablename) {
            $table = new $tablename(array('componentClass' => $this->_class));
            $where = array(
                'component_id = ?' => $componentId
            );
            return $table->fetchAll($where);
        }
        return array();
    }

    public function delete($componentId)
    {
        $row = $this->_getRow($componentId);
        if ($row) {
            $row->delete();
        }
    }

    public function getDuplicateProgressSteps($source)
    {
        $ret = 0;
        $s = array('inherit' => false, 'ignoreVisible'=>true);
        foreach ($source->getChildComponents($s) as $c) {
            $ret += $c->generator->getDuplicateProgressSteps($c);
        }
        return $ret;
    }

    public function duplicate($source, $target, Zend_ProgressBar $progressBar = null)
    {
        if ($model = $source->getComponent()->getOwnModel()) {
            $row = $model->getRow($source->dbId);
            if ($row) {
                $newRow = $row->duplicate(array(
                    'component_id' => $target->dbId
                ));
            }
        }

        $s = array('inherit' => false, 'ignoreVisible'=>true);
        foreach ($source->getChildComponents($s) as $c) {
            $c->generator->duplicateChild($c, $target, $progressBar);
        }
    }

    public function makeVisible($source)
    {
        foreach ($source->getChildComponents(array('inherit' => false, 'ignoreVisible'=>true)) as $c) {
            $c->generator->makeChildrenVisible($c);
        }
    }

    function createFormTable($tablename, $fields)
    {
        if (!$this->_tableExists($tablename)) {
            $f = array();
            $f['component_id'] = 'varchar(255) NOT NULL';
            $f = array_merge($f, $fields);

            $sql = "CREATE TABLE `$tablename` (";
            foreach ($f as $field => $data) {
                $sql .= " `$field` $data," ;
            }
            $sql .= 'PRIMARY KEY (component_id))';
            $sql .= 'ENGINE=InnoDB DEFAULT CHARSET=utf8';
            Vps_Registry::get('db')->query($sql);

            if (isset($fields['vps_upload_id'])) {
                Vps_Registry::get('db')->query("ALTER TABLE $tablename
                    ADD INDEX (vps_upload_id)");
                Vps_Registry::get('db')->query("ALTER TABLE $tablename
                    ADD FOREIGN KEY (vps_upload_id)
                    REFERENCES vps_uploads (id)
                    ON DELETE RESTRICT ON UPDATE RESTRICT");
            }
            return true;
        }
        return false;
    }

    protected function _tableExists($tablename)
    {
        return in_array($tablename, Vps_Registry::get('db')->listTables());
    }

    public function getCardForms()
    {
        $ret = array();
        $title = Vpc_Abstract::getSetting($this->_class, 'componentName');
        $title = str_replace('.', ' ', $title);
        $ret[] = array(
            'form' => Vpc_Abstract_Form::createComponentForm($this->_class, 'child'),
            'title' => $title,
        );
        return $ret;
    }

    public function getPagePropertiesForm()
    {
        return null;
    }
}
