<div class="<?=$this->cssClass?><?if($this->isPosted){?> kwfImportant<?}?>">
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
    <div class="webStandard webForm kwcForm">
    <form action="<?= htmlspecialchars($this->action) ?>" method="<?=$this->method?>"<? if($this->isUpload) { ?> enctype="multipart/form-data"<? } ?>>

        <?=$this->component($this->paragraphs)?>

        <div class="submitWrapper">
            <div class="beforeButton"></div>
            <div class="button">
                <div class="saving"></div>
                <button class="submit" type="submit" name="<?= $this->formName ?>" value="submit">
                    <span><?=$this->submitCaption?></span>
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
    </div>
    <? } ?>
    <?php
}
?>
</div>