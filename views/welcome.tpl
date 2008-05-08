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
            <?= $content ?>
        </div>
    <?php } else { ?>
        <div class="welcomeContent">
            <h1><?= $this->application['name'] . ' ' . trlVps('Version') . ' ' . $this->application['version'] ?></h1>
        </div>
    <?php } ?>
    <div class="footer">
        <?php if($content != '') { ?>
            <p><?= $this->application['name'] . ' ' . trlVps('Version') . ' ' . $this->application['version'] ?></p>
        <?php } ?>
        <h2><?= $this->application['vps']['name'] . ' ' . trlVps('Version') . ' ' . $this->application['vps']['version'] ?></h2>
        <img id="enteWelcome" src="/assets/vps/images/welcome/ente.jpg" alt="" height="30" width="54" />
    </div>
</div>
