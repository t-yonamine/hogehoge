@props(['action' => '', 'disabled' => false])
<div id="footer">
    <button type="submit" name="action" value="{{ $action }}" @disabled($disabled)>完了</button>
</div>
