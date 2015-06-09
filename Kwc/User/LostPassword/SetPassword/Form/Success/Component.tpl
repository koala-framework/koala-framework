<div class="kwcUserFormSuccess <?=$this->cssClass?>">
    <input type="hidden" value="<?=htmlspecialchars(json_encode($this->config))?>" />
    <p>
        <strong><?=$this->data->trlKwf('Your new password has been set.')?></strong>
    </p>
    <p>
        <?=$this->data->trlKwf('You were logged in, automatically')?><br />
        <a href="/"><?=$this->data->trlKwf('Click here')?></a>, <?=$this->data->trlKwf('to get back to the Startpage')?>.
    </p>
</div>