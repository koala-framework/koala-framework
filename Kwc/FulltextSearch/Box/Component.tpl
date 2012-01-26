<? if ($this->searchPage) { ?>
<div class="<?=$this->cssClass?>">
    <form action="<?=$this->searchPage->url?>">
        <input class="query kwfClearOnFocus" name="query" value="<?= $this->data->trlKwf('Search term'); ?>" />
        <input class="submit" type="submit" value="<?=$this->data->trlKwf('Search')?>" />
    </form>
</div>
<? } ?>

