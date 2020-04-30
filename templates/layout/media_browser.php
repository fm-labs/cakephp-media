<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <title><?= $this->fetch('title') ?> [MediaBrowser]</title>
    <meta name=viewport content="width=device-width, initial-scale=1">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="robots" content="noindex,nofollow">

    <?= $this->Html->meta('icon') ?>
    <?= $this->fetch('meta') ?>

    <?= $this->fetch('cssAdmin') ?>
    <?= $this->Html->css('Admin.bootstrap.min'); ?>
    <?= $this->Html->css('Admin.admin'); ?>
    <?= $this->fetch('css') ?>

    <?= $this->fetch('script') ?>
    <?= $this->Html->script('Admin.bootstrap.min'); ?>
    <?= $this->Html->script('Admin.be-ui'); ?>
    <?= $this->fetch('scriptAdmin'); ?>
</head>
<body>
    <h1>Media Browser</h1>
    <div id="media" class="container-fluid">
        <?= $this->fetch('content'); ?>
    </div>
    <?= $this->fetch('scriptAdmin'); ?>
    <?= $this->fetch('script'); ?>
</body>
</html>