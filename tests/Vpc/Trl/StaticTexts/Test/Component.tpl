trlTest: <?= $this->trlTest; ?><br />
trlcTest: <?= $this->trlcTest; ?><br />
trlpTest1: <?= $this->trlpTest1; ?><br />
trlpTest2: <?= $this->trlpTest2; ?><br />
trlcpTest1: <?= $this->trlcpTest1; ?><br />
trlcpTest2: <?= $this->trlcpTest2; ?><br />

<br />

trlVpsTest: <?= $this->trlVpsTest; ?><br />
trlcVpsTest: <?= $this->trlcVpsTest; ?><br />
trlpVpsTest1: <?= $this->trlpVpsTest1; ?><br />
trlpVpsTest2: <?= $this->trlpVpsTest2; ?><br />
trlcpVpsTest1: <?= $this->trlcpVpsTest1; ?><br />
trlcpVpsTest2: <?= $this->trlcpVpsTest2; ?><br />

<br />
<br />

trlTestTpl: <?= $this->data->trl('Sichtbar'); ?><br />
trlcTestTpl: <?= $this->data->trlc('time', 'Am'); ?><br />
trlpTest1Tpl: <?= $this->data->trlp('Antwort', 'Antworten', 1); ?><br />
trlpTest2Tpl: <?= $this->data->trlp('Antwort', 'Antworten', 2); ?><br />
trlcpTest1Tpl: <?= $this->data->trlcp('test', 'Antwort', 'Antworten', 1); ?><br />
trlcpTest2Tpl: <?= $this->data->trlcp('test', 'Antwort', 'Antworten', 2); ?><br />

<br />

trlVpsTestTpl: <?= $this->data->trlVps('Visible'); ?><br />
trlcVpsTestTpl: <?= $this->data->trlcVps('time', 'On'); ?><br />
trlpVpsTest1Tpl: <?= $this->data->trlpVps('reply', 'replies', 1); ?><br />
trlpVpsTest2Tpl: <?= $this->data->trlpVps('reply', 'replies', 2); ?><br />
trlcpVpsTest1Tpl: <?= $this->data->trlcpVps('test', 'reply', 'replies', 1); ?><br />
trlcpVpsTest2Tpl: <?= $this->data->trlcpVps('test', 'reply', 'replies', 2); ?><br />
