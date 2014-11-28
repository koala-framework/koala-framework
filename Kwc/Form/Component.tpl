<div class="<?=$this->cssClass?><?if($this->isPosted){?> kwfImportant<?}?>" data-width="100%">
    <input type="hidden" class="config" value="<?= htmlspecialchars(Zend_Json::encode($this->config)) ?>" />
<?php
if ($this->showSuccess) {
    echo $this->component($this->success);
} else if ($this->message) {
    echo '<div class="webStandard webForm kwcForm">';
    echo $this->message;
    echo '</div>';
} else {
    if ($this->errors) {
        echo '<div class="webStandard kwcFormError webFormError">';
        echo '<p class="error">'.$this->placeholder['error'].':</p>';
        echo '<ul>';
        foreach ($this->errors as $error) {
            echo '<li>' . htmlspecialchars($error) . '</li>';
        }
        echo '</ul>';
        echo '</div>';
    }
    ?>
    <? if ($this->form) { ?>
    <div class="webStandard webForm kwcForm" data-width="100%">
    <? if ($this->header) echo $this->component($this->header)?>
    <form action="<?= htmlspecialchars($this->action) ?>" method="<?=$this->method?>"<? if($this->isUpload) { ?> enctype="multipart/form-data"<? } ?>>
        <?php $this->formField($this->form) ?>
        <div class="spEmail">
            <label for="<?= $this->data->componentId ?>-sp-email"><?=$this->data->trlKwf('Leave empty')?>:</label>
            <input id="<?= $this->data->componentId ?>-sp-email" name="<?= $this->formName ?>-sp-email" /> <? /* spam protection, named email so bots think they should fill this */ ?>
        </div>
        <div class="submitWrapper <?=$this->buttonClass?>">
            <div class="beforeButton"></div>
            <div class="button">
                <div class="saving"></div>
                <button class="submit" type="submit" name="<?= $this->formName ?>" value="submit">
                    <span><?= $this->placeholder['submitButton'] ?></span>
                </button>
            </div>
            <div class="afterButton"></div>
            <div class="clear"></div>
        </div>
        <div class="clear"></div>
        <? if ($this->method == 'get') { ?>
            <? /* IE schickt bei enter-taste den submit-value nicht mit */ ?>
            <input type="hidden" name="<?= $this->formName ?>" value="submit" />
        <? } ?>

        <? /* damit wir wissen ob gepostet wurde und ob wir laden sollen */ ?>
        <input type="hidden" name="<?= $this->formName ?>-post" value="post" />

        <? if ($this->formId) { ?>
            <? /* to pass id's even if we send by ajax */ ?>
            <input type="hidden" name="<?= $this->formName ?>-id" value="<?= $this->formId; ?>" />
            <input type="hidden" name="<?= $this->formName ?>-idHash" value="<?= $this->formIdHash; ?>" />
        <? } ?>
    </form>
    <? if ($this->footer) echo $this->component($this->footer)?>
    </div>
    <? } ?>
    <?php
}
?>
</div>
