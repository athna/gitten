<nav class="repository-breadcrumb">
  <div class="content">
    <? include "components/rev-chooser.php"?>
    <? foreach ($repoFile->getParents() as $parent): ?>
      <a href="<?=$view == "commits" ? $parent->getCommitsUrl() : $parent->getUrl()?>/">
        <?=htmlspecialchars($parent->getName())?>
      </a> /
    <? endforeach ?>
    <? if ($repoFile->isDirectory()): ?>
      <a href="<?=$view == "commits" ? $repoFile->getCommitsUrl() : $repoFile->getUrl()?>/">
        <?=htmlspecialchars($repoFile->getName())?>
      </a> /
    <? else: ?>
      <?=htmlspecialchars($repoFile->getName())?>
    <? endif ?>
  </div>
</nav>
