<div class="<?=$this->cssClass?>">
    <p>
        <strong><?=$this->data->trlKwf('Your new password has been set.')?></strong>
    </p>
    <p>
        <?=$this->data->trlKwf('You were logged in, automatically')?><br />
        <a href="/"><?=$this->data->trlKwf('Click here')?></a>, <?=$this->data->trlKwf('to get back to the Startpage')?>.

        <script type="text/javascript">
            window.setTimeout("window.location.href = '/'", 3000);
        </script>
    </p>
</div>