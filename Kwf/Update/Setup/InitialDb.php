<?php
class Kwf_Update_Setup_InitialDb extends Kwf_Update_Sql
{
    protected $_dumpFile;
    public function __construct($dumpFile)
    {
        $this->_dumpFile = $dumpFile;
        parent::__construct($dumpFile, null);
    }

    public function update()
    {
        if (file_exists($this->_dumpFile)) {
            $this->sql = file_get_contents($this->_dumpFile);
            parent::update();
        }
    }
}
