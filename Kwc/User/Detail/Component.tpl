<div class="<?=$this->rootElementClass?>">
    <h1 class="mainHeadline"><?=$this->data->trlKwf('Userprofile')?></h1>
    <?php foreach ($this->items as $key => $name) { ?>
        <?php if ($this->hasContent($this->$key)) { ?>
            <?php if($name != '') { echo "<h1 class='mainHeadline'>$name</h1>"; } ?>
            <?= $this->component($this->$key); ?>
        <?php } ?>
    <?php } ?>
</div>
