<div class="<?=$this->cssClass?>">
    <h1><?=trlKwf('Logged In')?></h1>
    <p><?=trlKwf('You have been logged in sucessfully.')?></p>
    <p><?=trlKwf("If the needed page doesn't load automatically,")?>
    <?=$this->componentLink($this->redirectTo, trlKwf('please click here'))?>.</p>
    <script type="text/javascript">
        window.setTimeout("window.location.href = '<?=$this->redirectTo->url?>'", 2500);
    </script>
</div>