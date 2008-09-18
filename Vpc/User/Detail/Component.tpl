<div class="<?=$this->cssClass?>">
    <h1 class="mainHeadline"><?=trlVps('Userprofile')?></h1>
    <?php foreach ($this->items as $key => $name) { ?>
        <?=$this->ifHasContent($this->$key)?>
            <?php if($name != '') { echo "<h2>$name</h2>"; } ?>
            <?= $this->component($this->$key); ?>
        <?=$this->ifHasContent()?>
    <?php } ?>
</div>