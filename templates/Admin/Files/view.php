<?php
$this->assign('title', __d('media', 'View file'));
$this->assign('heading', $this->request->getQuery('file'));
?>
<div class="view">

    <pre style="width: 100%; overflow-y: scroll; max-height: 700px;"><?= h($contents); ?></pre>

</div>