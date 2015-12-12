<?php
class Kwc_Trl_StaticTexts_Test_Component extends Kwc_Abstract
{
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);

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
