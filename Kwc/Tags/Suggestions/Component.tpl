<div class="<?=$this->rootElementClass?>">
    <input type="hidden" class="config" value="<?=Kwf_Util_HtmlSpecialChars::filter(json_encode($this->config))?>" />
    <form>
        <div class="kwfUp-kwfField">
            <input value="<?=$this->data->trlKwf('Enter tag...')?>" class="kwfUp-kwfClearOnFocus tag" name="tag" />
            <input type="submit" class="submit" />
            <div class="kwfUp-clear"></div>
        </div>
    </form>
</div>
