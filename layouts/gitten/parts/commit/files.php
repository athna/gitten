<p class="file-summary">
  <?=$numFiles?> changed files, <?=$additions?> additions, <?=$deletions?>
  deletions.
</p>
<table class="file-list">
  <? foreach ($files as $file): ?>
    <tr>
      <td class="type type-<?=$file->getType()?>"><span><?=$file->getType()?></span></td>
      <td class="filename">
        <a href="#diff-<?=$file->getIndex()?>"><?=htmlspecialchars($file->getFilename())?></a>
      </td>
      <td class="modifications" title="<?=$file->getAdditions()?> additions, <?=$file->getDeletions()?> deletions">
        <?=$file->getModifications()?>
        <div class="changemeter">
          <? $addWidth = $file->getAdditions() ? round(100 * $file->getAdditions() / $maxModifications) : 0 ?>
          <? $delWidth = $file->getDeletions() ? round(100 * $file->getDeletions() / $maxModifications) : 0 ?>
          <div class="additions" style="width:<?=$addWidth?>%"></div>
          <div class="deletions" style="left:<?=$addWidth?>%;width:<?=$delWidth?>%"></div>
        </div>
      </td>
    </tr>
  <? endforeach ?>
</table>