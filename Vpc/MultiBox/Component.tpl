<div class="<?=$this->cssClass ?>">
    <?php foreach ($this->boxes as $box) { ?>
        <div class="multiBoxBox">
        <?= $this->component($box); ?>
        </div>
    <? } ?>
</div>