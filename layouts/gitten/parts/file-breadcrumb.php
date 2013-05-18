<nav class="file-breadcrumb <?=$localFile->getType()?>">
  <div class="content">
    <h1>
      <a href=".">git</a> /
      <? if (!$localFile->isRoot()): ?>
        <? foreach ($localFile->getParents() as $parent): ?>
          <a href="<?=$parent->getUrl()?>/"><?=$parent->getName()?></a> /
        <? endforeach ?>
        <? if ($localFile->isRepository()): ?>
          <a href="<?=$localFile->getUrl()?>/"><?=$localFile->getName()?></a>
        <? elseif ($localFile->isDirectory()): ?>
          <a href="<?=$localFile->getUrl()?>/"><?=$localFile->getName()?></a> /
        <? else: ?>
          <?=$localFile->getName()?>
        <? endif ?>
      <? endif ?>
    </h1>

    <? if ($localFile->isRepository()): ?>
      <div class="repo-protocol">
        <? include "components/protocol-chooser.php"; ?>
      </div>
    <? endif ?>

  </div>
</nav>
