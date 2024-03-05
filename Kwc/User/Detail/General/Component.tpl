<div class="<?=$this->rootElementClass?>">
    <h3>Name</h3>
    <?=Kwf_Util_HtmlSpecialChars::filter($this->row->title . ' ' . $this->row->firstname . ' ' . $this->row->lastname)?>

    <h3><?=$this->data->trlKwf('Member since')?></h3>
    <?=$this->date($this->row->created)?>

    <?php if (isset($this->row->last_login_web)) { ?>
        <h3><?=$this->data->trlKwf('Latest online')?></h3>
        <?=$this->timestamp($this->row->last_login_web)?>
    <?php } ?>

</div>
