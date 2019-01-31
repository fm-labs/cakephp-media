<?php
$this->assign('title', __('View file'));
$this->assign('heading', $this->request->query('file'));
?>
<div class="view">

    <pre style="width: 100%; overflow-y: scroll; max-height: 700px;"><?= h($contents); ?></pre>

</div>