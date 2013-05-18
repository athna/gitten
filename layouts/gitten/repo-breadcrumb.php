<nav class="repository-breadcrumb">
  <div class="content">
    <? include "components/rev-chooser.php"?>
    <? $repoFile = $this->getRepoFile() ?>
    <? foreach ($repoFile->getParents() as $parent): ?>
      <a href="<?=$parent->getUrl()?>/"><?=$parent->getName()?></a> /
    <? endforeach ?>
    <? if ($repoFile->isDirectory()): ?>
      <a href="<?=$repoFile->getUrl()?>/"><?=$repoFile->getName()?></a> /
    <? else: ?>
      <?=$repoFile->getName()?>
    <? endif ?>
  </div>
</nav>
