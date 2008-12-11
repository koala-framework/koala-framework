<?php
class Vps_Update_Action_Rrd_AddDs extends Vps_Update_Action_Rrd_Abstract
{
    public $name;
    public $type = 'COUNTER';
    public $minimalHeartbeat = 120;
    public $min = 0;
    public $max = 1000;

    public function update()
    {
        if (!file_exists($this->file)) return array();

        $sXml = $this->_dump();

        foreach ($sXml->rra as $rra) {
            $ds = $rra->cdp_prep->addChild('ds');
            $ds->primary_value = 'NaN';
            $ds->secondary_value = 'NaN';
            $ds->value = 'NaN';
            $ds->unknown_datapoints = '0';
            foreach ($rra->database->row as $r) {
                $r->addChild('v', 'NaN');
            }
        }
        
        $xml = dom_import_simplexml($sXml);
        $doc = $xml->ownerDocument;
        $ds = $doc->createElement('ds');
        $ds = $xml->insertBefore($ds, $doc->getElementsByTagName('rra')->item(0));

        $ds->appendChild($doc->createElement('name', $this->name));
        $ds->appendChild($doc->createElement('type', $this->type));
        $ds->appendChild($doc->createElement('minimal_heartbeat', $this->minimalHeartbeat));
        $ds->appendChild($doc->createElement('min', $this->min));
        $ds->appendChild($doc->createElement('max', $this->max));

        $ds->appendChild($doc->createElement('last_ds', 'U'));
        $ds->appendChild($doc->createElement('value', 'NaN'));
        $ds->appendChild($doc->createElement('unknown_sec', 3));

        $this->_restore(simplexml_import_dom($xml));
        return array();
    }
}
