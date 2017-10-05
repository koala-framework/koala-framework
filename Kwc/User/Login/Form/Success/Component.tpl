<div class="<?=$this->rootElementClass?>">
    <input type="hidden" class="redirectTo" value="<?=Kwf_Util_HtmlSpecialChars::filter($this->redirectToUrl)?>" />
    <h1><?=$this->data->trlKwf('Logged In')?></h1>
    <p><?=$this->data->trlKwf('You have been logged in sucessfully.')?></p>
    <p><?=$this->data->trlKwf("If the needed page doesn't load automatically,")?>
    <?=$this->link($this->redirectToUrl, $this->data->trlKwf('please click here'))?>.</p>
</div>
