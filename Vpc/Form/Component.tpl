<div class="<?=$this->cssClass?>">
<?php
if ($this->showSuccess) {
    echo $this->component($this->success);
} else {
    if ($this->errors) {
        echo '<div class="webStandard vpcFormError webFormError">';
        echo '<h1>'.$this->placeholder['error'].':</h1>';
        echo '<ul>';
        foreach ($this->errors as $error) {
            echo "<li>$error</li>";
        }
        echo '</ul>';
        echo '</div>';
    }
    ?>
    <div class="webStandard webForm vpcForm">
    <form action="<?= $this->action ?>" method="<?=$this->method?>"<? if($this->isUpload) { ?> enctype="multipart/form-data"<? } ?>>
        <?php $this->formField($this->form) ?>
        <div class="submitWrapper">
            <span class="beforeButton"></span><span class="button"><button class="submit" type="submit" name="<?= $this->formName ?>" value="submit"><?= $this->placeholder['submitButton'] ?></button></span><span class="afterButton"></span>
            <div class="clear"></div>
        </div>
        <div class="clear"></div>
        <? if ($this->method == 'get') { ?>
            <? /* IE schickt bei enter-taste den submit-value nicht mit */ ?>
            <input type="hidden" name="<?= $this->formName ?>" value="submit" />
        <? } ?>

        <? /* damit wir wissen ob gepostet wurde und ob wir laden sollen */ ?>
        <input type="hidden" name="<?= $this->formName ?>-post" value="post" />
    </form>
    </div>
    <?php
}
?>
</div>