<div class="<?=$this->cssClass?>">
    <h1><?=trlVps('Logged In')?></h1>
    <p><?=trlVps('You have been logged in sucessfully.')?></p>
    <p><?=trlVps("If the needed page doesn't load automatically,")?>
    <a href="<?=$this->redirectTo?>"><?=trlVps('please click here')?></a>.</p>
    <script type="text/javascript">
        window.setTimeout("window.location.href = '<?=$this->redirectTo?>'", 2500);
    </script>
</div>