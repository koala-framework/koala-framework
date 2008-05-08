<?php
if ($this->type == 'select') {
    echo '<select name="' . $this->name . '"';
    if ($this->width) { echo 'style="width:' . $this->width . 'px;"'; }
        foreach ($this->options as $option) {
            echo '<option value="' . $option['value'] . '"';
            if ($option['checked']) { echo ' selected="selected"'; }
            echo '>' . $option['text'] . '</option>';
        }
    echo '</select>';
} else {
    foreach ($this->options as $option) {
        echo '<input type="radio" name="' . $this->name . '" value="' . $option['value'] . '"';
        if ($option['checked']) { echo ' checked'; }
        echo '/>';
        echo $option['text'];
        if ($this.type != 'radio_horizontal') { echo '<br />'; }
    }
}
?>
