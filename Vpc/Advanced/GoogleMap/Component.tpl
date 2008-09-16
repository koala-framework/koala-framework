<div class="<?=$this->cssClass?>">

    <div class="text"><?php echo $this->component($this->text) ?><br /></div>
    <input type="hidden" class="options" value='<?= $this->options ?>' />

    <div class="container"></div>

    <form action="#" class="fromAddress printHidden">
        <input type="text" class="textBefore"/>
        <input type="submit" value="<?= trlVps('Show Route') ?>" class="submitOn"/>
    </form>

    <div class="mapDirSuggestParent">
        <b><?= trlVps('Suggestions') ?></b>
        <ul class="mapDirSuggest"></ul>
    </div>

    <div class="mapDir"></div>
</div>

