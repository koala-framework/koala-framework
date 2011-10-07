<div class="<?=$this->cssClass?>">
    <p>
        <strong><?=trlKwf('Your useraccount was successfully activated.')?></strong>
    </p>
    <p>
        <?=trlKwf('You were logged in, automatically')?><br />
        <a href="/"><?=trlKwf('Click here')?></a>, <?=trlKwf('to get back to the Startpage')?>.

        <script type="text/javascript">
            window.setTimeout("window.location.href = '/'", 3000);
        </script>
    </p>
</div>