<div class="kwcUserFormSuccess <?=$this->rootElementClass?>">
    <input type="hidden" value="<?=htmlspecialchars(json_encode($this->config))?>" />
    <p>
        <strong><?=$this->data->trlKwf('Your useraccount was successfully activated.')?></strong>
    </p>
    <p>
        <?=$this->data->trlKwf('You were logged in, automatically')?><br />
        <a href="/"><?=$this->data->trlKwf('Click here')?></a>, <?=$this->data->trlKwf('to get back to the Startpage')?>.
    </p>
</div>