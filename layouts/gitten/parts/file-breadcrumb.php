<nav class="file-breadcrumb <?=$file->getType()?>">
  <div class="content">
    <h1>
      <a href=".">git</a> /
      <? if (!$file->isRoot()): ?>
        <? foreach ($file->getParents() as $parent): ?>
          <a href="<?=$parent->getUrl()?>/"><?=$parent->getName()?></a> /
        <? endforeach ?>
        <? if ($file->isRepository()): ?>
          <a href="<?=$file->getUrl()?>/"><?=$file->getName()?></a>
        <? elseif ($file->isDirectory()): ?>
          <a href="<?=$file->getUrl()?>/"><?=$file->getName()?></a> /
        <? else: ?>
          <?=$file->getName()?>
        <? endif ?>
      <? endif ?>
    </h1>

    <? if ($file->isRepository()): ?>
      <div class="repo-protocol">
        <? include "components/protocol-chooser.php"; ?>
      </div>
    <? endif ?>

  </div>
</nav>
