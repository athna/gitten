<div id="diff-<?=$file->getIndex()?>" class="box">
  <div class="header">
    <ul class="actions">
      <li>
        <?=$file->getParentHash()?>
        &#x2192;
        <?=$file->getCommitHash()?>
      </li>
    </ul>
    <ul class="info">
      <li><?=htmlspecialchars($file->getFilename()) ?></li>
    </ul>
  </div>
  <? require $file->isBinary() ? "binaryheader.php" : "textheader.php" ?>
