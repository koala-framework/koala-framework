<?php
class Vpc_Forum_User_Model extends Vps_Db_Table_Abstract
{
    protected $_name = 'vpc_forum_users';
    protected $_rowClass = 'Vpc_Forum_User_Row';

    protected function _fetch($where = null, $order = null, $count = null, $offset = null)
    {
        $ret = parent::_fetch($where, $order, $count, $offset);
        if (!$ret) {
            $newWhere = $where;
            if (!is_array($newWhere)) $newWhere = array($newWhere);
            $whereIdOnly = null;
            foreach ($newWhere as $key => $val) {
                if (preg_match('/^id\s*=/', trim($key)) || preg_match('/^id\s*=/', trim($val))) {
                    $whereIdOnly = array($key => $val);
                    break;
                }
            }

            if ($whereIdOnly) {
                $userRow = Zend_Registry::get('userModel')->fetchRow($whereIdOnly);
                if ($userRow) {
                    $newRow = $this->createRow();
                    $newRow->id = $userRow->id;
                    $newRow->save();
                    return parent::_fetch($where, $order, $count, $offset);
                }
            }
        }
        return $ret;
    }
}