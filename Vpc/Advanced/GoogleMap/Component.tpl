<div class="<?=$this->cssClass?>">
<? if($this->data->hasContent()) { ?>
    <div class="text"><?php echo $this->component($this->text) ?><br /></div>
    <input type="hidden" class="options" value="<?= str_replace("\"", "'", $this->options) ?>" />

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
<? } else { ?>
    <?=trlVps('coordinates not entered')?>
<? } ?>
</div>

