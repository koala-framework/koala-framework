<div class="<?=$this->rootElementClass?>">
    <?php if ($this->target) { ?>
        <p><?=$this->componentLink($this->target)?></p>
        <?=$this->listHtml?>
    <?php } ?>
</div>
