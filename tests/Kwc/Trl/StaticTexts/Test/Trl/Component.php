<?php
class Kwc_Trl_StaticTexts_Test_Trl_Component extends Kwc_Chained_Trl_Component
{
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);

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

        $ret['trlKwfTest'] = $this->getData()->trlKwf('Visible');
        $ret['trlcKwfTest'] = $this->getData()->trlcKwf('time', 'On');
        $ret['trlpKwfTest1'] = $this->getData()->trlpKwf('reply', 'replies', 1);
        $ret['trlpKwfTest2'] = $this->getData()->trlpKwf('reply', 'replies', 2);
        $ret['trlcpKwfTest1'] = $this->getData()->trlcpKwf('test', 'reply', 'replies', 1);
        $ret['trlcpKwfTest2'] = $this->getData()->trlcpKwf('test', 'reply', 'replies', 2);

        return $ret;
    }
}
