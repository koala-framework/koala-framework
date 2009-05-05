<?php
class Vps_Update_Action_Rrd_DropDs extends Vps_Update_Action_Rrd_Abstract
{
    public $name;

    public function update()
    {
        if (!file_exists($this->file)) return array();

        if (is_string($this->name)) $this->name = array($this->name);
        foreach ($this->name as &$n) {
            $n = Vps_Util_Rrd_Field::escapeField($n);
        }

        if (!$this->silent) {
            echo "dropping rrd fields: ".implode($this->name)."\n";
        }

        $xml = $this->_dump();
        $found = false;
        $index = 0;
        foreach ($xml->ds as $ds) {
            if (in_array(trim($ds->name), $this->name)) {
                $ds = dom_import_simplexml($ds);
                $ds->parentNode->removeChild($ds);
                $i = 0;
                foreach ($xml->rra->cdp_prep->ds as $ds2) {
                    if ($i == $index) {
                        $ds2 = dom_import_simplexml($ds2);
                        $ds2->parentNode->removeChild($ds2);
                        break;
                    }
                    $i++;
                }
                foreach ($xml->rra->database->row as $row) {
                    $i = 0;
                    foreach ($row->v as $v) {
                        if ($i == $index) {
                            $v = dom_import_simplexml($v);
                            $v->parentNode->removeChild($v);
                            break;
                        }
                        $i++;
                    }
                }
            }
            $index++;
        }

        $this->_restore($xml);
        return array();
    }

}
