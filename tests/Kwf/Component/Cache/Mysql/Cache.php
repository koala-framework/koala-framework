<?php
class Kwf_Component_Cache_Mysql_Cache extends Kwf_Component_Cache_Fnf
{
}

class Kwf_Component_Cache_Mysql_Cache1 extends Kwf_Component_Cache_Fnf
{
    protected function _beforeDatabaseDelete($select) {
        Kwf_Component_Data_Root::getInstance()->render();
    }
}

class Kwf_Component_Cache_Mysql_Cache2 extends Kwf_Component_Cache_Fnf
{
    protected function _beforeDatabaseDelete($select) {
        static $called = false;
        if (!$called) {
            $called = true;
            throw new Kwf_Exception();
        }
    }
}

class Kwf_Component_Cache_Mysql_Cache3 extends Kwf_Component_Cache_Fnf
{
    protected function _afterDatabaseDelete($select) {
        static $called = false;
        if (!$called) {
            $called = true;
            throw new Kwf_Exception();
        }
    }
}

class Kwf_Component_Cache_Mysql_Cache4 extends Kwf_Component_Cache_Fnf
{
    protected function _beforeDatabaseDelete($select) {
        static $called = false;
        if (!$called) {
            $called = true;
            throw new Kwf_Exception();
        }
    }
}
