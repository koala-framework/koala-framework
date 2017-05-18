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
            <h1><?= $this->application->name ?></h1>
        </div>
    <?php } ?>
    <div class="footer">
        <?php if($content != '') { ?>
            <p><?= $this->application->name ?></p>
        <?php } ?>
        <?php if (Kwf_Config::getValue('application.kwf.name') == 'Koala Framework') { ?>
            <h2><a href="http://www.koala-framework.org/" target="_blank"><?= $this->application->kwf->name . '</a> ' . ($this->application->kwf->version ? trlKwf('Version') . ' ' . $this->application->kwf->version : '') ?></h2>
        <?php } else { ?>
            <h2><?= $this->application->kwf->name . ' ' . ($this->application->kwf->version ? trlKwf('Version') . ' ' . $this->application->kwf->version : '') ?></h2>
        <?php } ?>
        <?=$this->image('/assets/kwf/images/welcome/ente.jpg', '', array('id'=>'enteWelcome'))?>
    </div>
</div>
