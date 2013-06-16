<? $pageId = "blob" ?>

<? include "parts/header.php" ?>
<? include "parts/file-breadcrumb.php" ?>
<? include "parts/repo-nav.php" ?>
<? include "parts/repo-breadcrumb.php" ?>

<div class="content">
  <div class="blob">
    <div class="header">
      <ul class="actions buttons">
        <li><a href="<?=$repoFile->getRawUrl()?>">Raw</a></li>
        <li><a href="#">Blame</a></li>
        <li><a href="#">History</a></li>
      </ul>
      <dl class="info">
        <dt class="fileMode">File mode</dt>
        <dd class="fileMode" title="<?=$repoFile->getMode()->getLongText()?>"><?=$repoFile->getMode()->getShortText()?></dd>
        <dt class="fileSize">File size</dt>
        <dd class="fileSize" title="<?=$repoFile->getSize()->getLongText()?>"><?=$repoFile->getSize()->getShortText()?></dd>
      </dl>
    </div>
    <div class="body">
      <? if ($repoFile->isBinary()): ?>
        <? if ($repoFile->isImage()): ?>
          <? include "parts/blob/image.php" ?>
        <? else: ?>
          Binary
        <? endif ?>
      <? else: ?>
        <? if ($repoFile->getMimeType() == "text/x-web-markdown"): ?>
          Markdown
        <? else: ?>
          <? include "parts/blob/text.php" ?>
        <? endif ?>
      <? endif ?>
    </div>
  </div>
</div>

<? include "parts/footer.php" ?>
