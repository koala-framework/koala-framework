<?php
class Kwf_Update_Action_Rrd_RenameDs extends Kwf_Update_Action_Rrd_Abstract
{
    public $name;
    public $newName;

    public function update()
    {
        if (!file_exists($this->file)) return array();

        $this->name = Kwf_Util_Rrd_Field::escapeField($this->name);
        $this->newName = Kwf_Util_Rrd_Field::escapeField($this->newName);

        if (!$this->silent) {
            echo "renaming rrd field: ".$this->name."\n";
        }
        
        $xml = $this->_dump();
        $found = false;
        foreach ($xml->ds as $ds) {
            if (trim($ds->name) == $this->name) {
                $ds->name = $this->newName;
                $found = true;
                break;
            }
        }
        if (!$found) {
            throw new Kwf_ClientException("Field '{$this->name}' not found");
        }
        $this->_restore($xml);
        return array();
    }

}
