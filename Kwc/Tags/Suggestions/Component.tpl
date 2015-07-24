<div class="<?=$this->rootElementClass?>">
    <input type="hidden" class="config" value="<?=htmlspecialchars(json_encode($this->config))?>" />
    <form>
        <div class="kwfField">
            <input value="<?=$this->data->trlKwf('Enter tag...')?>" class="kwfClearOnFocus tag" name="tag" />
            <input type="submit" class="submit" />
            <div class="clear"></div>
        </div>
    </form>
</div>