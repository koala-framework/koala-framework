<div class="<?=$this->cssClass?>">
    <h1><?=trlVps('Userprofile')?></h1>
    <?php foreach ($this->items as $key => $name) { ?>
    <?php if($name != '') { echo "<h2>$name</h2>"; } ?>
    <?= $this->component($this->$key); ?>
    <?php } ?>
</div>