<? include "parts/header.php" ?>
<? include "parts/file-breadcrumb.php" ?>
<? include "parts/repo-nav.php" ?>

<div class="content">

<? $diff = $repo->getCommitDiff() ?>

<? $deleted = $diff->getDeletedFiles() ?>
<? if (count($deleted) > 0): ?>
  <h2>Files deleted (<?=count($deleted)?>)</h2>
  <ul class="files">
    <? foreach ($deleted as $file): ?>
      <li>
        <span class="deleted-lines">-<?=$file->getDeletions()?></span>
        <a href="<?=htmlspecialchars($file->getUrl())?>">
          <?=htmlspecialchars($file->getFilename())?>
        </a>
      </li>
    <? endforeach ?>
  </ul>
<? endif ?>

<? $added = $diff->getAddedFiles() ?>
<? if (count($added) > 0): ?>
  <h2>Files added (<?=count($added)?>)</h2>
  <ul class="files">
    <? foreach ($added as $file): ?>
      <li>
        <span class="added-lines">+<?=$file->getAdditions()?></span>
        <a href="<?=htmlspecialchars($file->getUrl())?>">
          <?=htmlspecialchars($file->getFilename())?>
        </a>
      </li>
    <? endforeach ?>
  </ul>
<? endif ?>

<? $changed = $diff->getChangedFiles() ?>
<? if (count($changed) > 0): ?>
  <h2>Files changed (<?=count($changed)?>)</h2>
  <ul class="files">
    <? foreach ($changed as $file): ?>
      <li>
        <span class="added-lines">+<?=$file->getAdditions()?></span>
        <span class="deleted-lines">-<?=$file->getDeletions()?></span>
        <a href="<?=$repo->getCommitUrl()?>#diff-<?=$file->getIndex()?>">
          <?=htmlspecialchars($file->getFilename())?>
        </a>
      </li>
    <? endforeach ?>
  </ul>
<? endif ?>

<? while ($file = $diff->nextFile()): ?>
  <? if (!$file->isModification()) continue ?>
  <div id="diff-<?=$file->getIndex()?>" class="diff">
    <div class="header">
      <div class="filename"><?=htmlspecialchars($file->getFilename());?></div>
      <ul class="actions buttons">
        <li><a href="<?=htmlspecialchars($file->getSrcUrl())?>">View old file</a></li>
        <li><a href="<?=htmlspecialchars($file->getDestUrl())?>">View new file</a></li>
      </ul>
    </div>
    <table class="chunks">
      <? while ($chunk = $diff->nextChunk()): ?>
        <?
          $sourceLineNumber = $chunk->getSourceLine();
          $destLineNumber = $chunk->getDestLine();
          $sourceLineNumbers = "";
          $destLineNumbers = "";
          $lineTypes = "";
          $lines = "";
          while ($line = $diff->nextChunkLine())
          {
              $lineTypes .= $line->getType() . "\n";
              $lines .= $line->getLine();
              if (!$line->isAddition())
              {
                  $sourceLineNumbers .= $destLineNumber;
                  $sourceLineNumber++;
              }
              if (!$line->isDeletion())
              {
                  $destLineNumbers .= $destLineNumber;
                  $destLineNumber++;
              }
              $sourceLineNumbers .= "\n";
              $destLineNumbers .= "\n";
          }
        ?>
        <tr>
          <td class="source-line-numbers"><?=$sourceLineNumbers?></td>
          <td class="dest-line-numbers"><?=$destLineNumbers?></td>
          <td class="line-types"><?=$lineTypes?></td>
          <td class="lines"><?=$lines?></td>
        </tr>
      <? endwhile ?>
    </table>
  </div>
<? endwhile ?>
</div>

<? include "parts/footer.php" ?>
