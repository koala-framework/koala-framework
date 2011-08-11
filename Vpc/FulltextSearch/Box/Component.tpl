<div class="<?=$this->cssClass?>">
    <form action="<?=$this->searchPage->url?>">
        <input class="query vpsClearOnFocus" name="query" value="<?= $this->data->trlVps('Search term'); ?>" />
        <input class="submit" type="submit" value="<?=$this->data->trlVps('Search')?>" />
    </form>
</div>