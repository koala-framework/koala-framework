<?php
class Vps_Component_Cache_Meta_Static_UrlCache extends Vps_Component_Cache_Meta_Static_Model
{
    public function __construct($generator)
    {
        parent::__construct($generator->getModel());
        $this->_params['generator']['class'] = $generator->getClass();
        $this->_params['generator']['key'] = $generator->getGeneratorKey();
    }

    public static function getMetaType()
    {
        return self::META_TYPE_CLEANURLCACHE;
    }

    public static function getDeleteWhere($pattern, $row, $dirtyColumns, $params)
    {
        $ret = array();
        $generator = Vps_Component_Generator_Abstract::getInstance($params['generator']['class'], $params['generator']['key']);
        $s = new Vps_Component_Select();
        $pk = $row->getModel()->getPrimaryKey();
        if ($generator instanceof Vpc_Root_Category_Generator) {
            $s->whereId($row->$pk);
        } else {
            $s->whereId($generator->getIdSeparator().$row->$pk);
        }
        $s->ignoreVisible(true);
        foreach ($generator->getChildData(null, $s) as $c) {
            //TODO mehere sollten möglich sein
            $ret['db_id'] = $c->dbId;
        }
        return $ret;
    }

}
