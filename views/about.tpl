<h1><?= $this->application['name'] ?></h1>
<h1>
<?php if (Kwf_Config::getValue('application.kwf.name') == 'Koala Framework') { ?>
<a href="http://www.koala-framework.org/" onclick="window.open(this.href); return false;" >
    <?= $this->application['kwf']['name'] . '</a> ' . trlKwf('Version') . ' ' . $this->application['kwf']['version'] ?>
<p>License: <a href="http://www.opensource.org/licenses/BSD-2-Clause" onclick="window.open(this.href); return false;">BSD License</a></p>
<?php } else { ?>
    <?= $this->application['kwf']['name'] . '</a> ' . trlKwf('Version') . ' ' . $this->application['kwf']['version'] ?>
<?php } ?>
</h1>
<div class="footer">
    <p>Copyright 2007-<?=date('Y')?> <a href="http://www.vivid-planet.com/" onclick="window.open('http://www.vivid-planet.com/'); return false;">Vivid Planet Software GmbH</a></p>
    <img id="enteWelcome" src="/assets/kwf/images/welcome/ente.jpg" alt="" height="30" width="54" />
</div>
