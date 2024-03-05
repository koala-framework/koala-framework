<div class="kwcUserFormSuccess <?=$this->rootElementClass?>">
    <input type="hidden" value="<?=Kwf_Util_HtmlSpecialChars::filter(json_encode($this->config))?>" />
    <p>
        <strong><?=$this->data->trlKwf('Your useraccount was successfully activated.')?></strong>
    </p>
    <p>
        <?=$this->data->trlKwf('You were logged in, automatically')?><br />
        <p>
            <?=$this->data->trlKwf("If the needed page doesn't load automatically,")?>
            <?=$this->link($this->config['redirectUrl'], $this->data->trlKwf('please click here'))?>.
        </p>
    </p>
</div>
