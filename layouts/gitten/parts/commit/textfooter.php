</pre>
        </td>
      </tr>
      <tr class="hide">
        <td class="line-numbers"><?=$maxSourceLine?></td>
        <td class="line-numbers"><?=$maxDestLine?></td>
      </tr>
    </table>
    <table class="line-numbers">
      <tr>
        <td class="line-numbers">
          <? foreach ($sourceLines as $line) { ?>
            <? if ($line && $line != "@@") { ?><a href="<?=htmlspecialchars($parentBlobUrl)?>#L<?=$line?>"><?=$line?></a><? } else { ?><?=$line?><? } ?><br />
          <? } ?>
        </td>
        <td class="line-numbers">
          <? foreach ($destLines as $line) { ?>
            <? if ($line && $line != "@@") { ?><a href="<?=htmlspecialchars($commitBlobUrl)?>#L<?=$line?>"><?=$line?></a><? } else { ?><?=$line?><? } ?><br />
          <? } ?>
        </td>
      </tr>
    </table>
  </div>
