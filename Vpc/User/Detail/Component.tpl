<div class="<?=$this->cssClass?>">
    <h1 class="mainHeadline"><?=trlVps('Userprofile')?></h1>
    <?php foreach ($this->items as $key => $name) { ?>
        <?if ($this->hasContent($this->$key)) {?>
            <?php if($name != '') { echo "<h1 class='mainHeadline'>$name</h1>"; } ?>
            <?= $this->component($this->$key); ?>
        <?}?>
    <?php } ?>
</div>