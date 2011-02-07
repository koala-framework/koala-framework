<table width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td>
            <?=htmlspecialchars($this->order->title.' '.$this->order->firstname.' '.$this->order->lastname);?>
        </td>
    </tr>
    <tr>
        <td>
            <?=htmlspecialchars($this->order->street);?>
        </td>
    </tr>
    <tr>
        <td>
            <?=htmlspecialchars($this->order->zip);?> <?=htmlspecialchars($this->order->city);?>
        </td>
    </tr>
    <tr>
        <td>
            <?=htmlspecialchars($this->order->country);?>
        </td>
    </tr>
</table>
