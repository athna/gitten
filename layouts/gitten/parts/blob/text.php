<? $content = $repoFile->getContent(); ?>
<? $lineCount = $repoFile->getLineCount(); ?>
<? $url = $repoFile->getUrl(); ?>
<table class="blob">
  <tr>
    <td class="line-numbers">
      <? for ($i = 1; $i <= $lineCount; $i++): ?>
        <a id="L<?=$i?>" href="<?=$url?>#L<?=$i?>"><?=$i?></a><br />
      <? endfor ?>
    </td>
    <td class="text-file"><div><?=htmlspecialchars($content);?></div></td>
  </tr>
</table>
