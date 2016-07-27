<div class="<?=$this->rootElementClass?>">
    <?php if ($this->placeholder['loginHeadline']) { ?>
        <h2><?=$this->placeholder['loginHeadline']?></h2>
    <?php } ?>
    <?=$this->component($this->login)?>
    <ul>
        <?php if ($this->register) { ?>
        <li><?=$this->componentLink($this->register, $this->data->trlKwf('Register'))?><?=$this->linkPostfix?></li>
        <?php } ?>
        <?php if ($this->lostPassword) { ?>
        <li><?=$this->componentLink($this->lostPassword, $this->data->trlKwf('Lost password'))?><?=$this->linkPostfix?></li>
        <?php } ?>
    </ul>
</div>
