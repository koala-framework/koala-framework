<?php if($this->image) { ?>
    <div id="welcomeImg">
        <img src="<?= $this->image ?>" width="<?= $this->imageSize['width'] ?>" height="<?= $this->imageSize['height'] ?>" />
    </div>
<?php } ?>
<div class="welcomeBox">
    <?php
        $content = strip_tags(trim($this->content));
    ?>
    <?php if($content != '') { ?>
        <div class="welcomeContent">
            <?= $this->content ?>
        </div>
    <?php } else { ?>
        <div class="welcomeContent">
            <h1><?= $this->application->name . ' ' . trlKwf('Version') . ' ' . $this->application->version ?></h1>
        </div>
    <?php } ?>
    <div class="footer">
        <?php if($content != '') { ?>
            <p><?= $this->application->name . ' ' . trlKwf('Version') . ' ' . $this->application->version ?></p>
        <?php } ?>
        <? if (Kwf_Config::getValue('application.kwf.name') == 'Koala Framework') { ?>
            <h2><a href="http://www.koala-framework.org/" target="_blank"><?= $this->application->kwf->name . '</a> ' . trlKwf('Version') . ' ' . $this->application->kwf->version ?></h2>
        <? } else { ?>
            <h2><?= $this->application->kwf->name . trlKwf('Version') . ' ' . $this->application->kwf->version ?></h2>
        <? } ?>
        <img id="enteWelcome" src="/assets/kwf/images/welcome/ente.jpg" alt="" height="30" width="54" />
    </div>
</div>
