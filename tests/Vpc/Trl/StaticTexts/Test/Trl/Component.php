<?php
class Vpc_Trl_StaticTexts_Test_Trl_Component extends Vpc_Chained_Trl_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        /* Wird im normalfall nicht in Trl Component überschrieben,
           weil man das sowieso nicht benötigt in den templateVars.
           Üblicherweise wird man da immer placeholder verwenden
           oder das trl direkt im template aufrufen (dort funktioniert
           es dann korrekt)
        */

        $ret['trlTest'] = $this->getData()->trl('Sichtbar');
        $ret['trlcTest'] = $this->getData()->trlc('time', 'Am');
        $ret['trlpTest1'] = $this->getData()->trlp('Antwort', 'Antworten', 1);
        $ret['trlpTest2'] = $this->getData()->trlp('Antwort', 'Antworten', 2);
        $ret['trlcpTest1'] = $this->getData()->trlcp('test', 'Antwort', 'Antworten', 1);
        $ret['trlcpTest2'] = $this->getData()->trlcp('test', 'Antwort', 'Antworten', 2);

        $ret['trlVpsTest'] = $this->getData()->trlVps('Visible');
        $ret['trlcVpsTest'] = $this->getData()->trlcVps('time', 'On');
        $ret['trlpVpsTest1'] = $this->getData()->trlpVps('reply', 'replies', 1);
        $ret['trlpVpsTest2'] = $this->getData()->trlpVps('reply', 'replies', 2);
        $ret['trlcpVpsTest1'] = $this->getData()->trlcpVps('test', 'reply', 'replies', 1);
        $ret['trlcpVpsTest2'] = $this->getData()->trlcpVps('test', 'reply', 'replies', 2);

        return $ret;
    }
}
