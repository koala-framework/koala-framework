<div class="<?=$this->cssClass?>">
    <input type="hidden" class="config" value="<?=htmlspecialchars(json_encode($this->config))?>" />
    <h2><?=$this->data->trlKwf('Tags')?></h2>
    <div class="tags">
        <?=implode(',', $this->tags)?>
    </div>

    <form>
        <div class="kwfField">
            <input value="<?=$this->data->trlKwf('Enter tag...')?>" class="kwfClearOnFocus tag" name="tag" />
            <input type="submit" class="submit" />
            <div class="clear"></div>
        </div>
    </form>
</div>
