<? require "header.php" ?>
<? require "file-breadcrumb.php" ?>

<div class="content">
  <table class="directory">
    <? foreach ($this->getFile()->getChildren() as $file): ?>
      <tr class="<?=$file->getType()?>">
        <td class="name"><a href="<?=$file->getUrl()?>"><?=$file->getName()?></a></td>
        <td class="size">
          <? if (!$file->isDirectory()): ?>
            <? $fileSize = $file->getSize(); ?>
            <span class="filesize"<? if ($fileSize->getSize() >= 1024): ?> title="<?=$fileSize->getLongText()?>"<? endif ?>>
              <?=$fileSize->getShortText()?>
            </span>
          <? endif?>
        </td>
        <td class="lastModified">
          <? if (!$file->isDirectory()): ?>
            <?=$file->getLastModified()->getHTML()?>
          <? endif ?>
        </td>
      </tr>
    <? endforeach ?>
  </table>
</div>

<? include "footer.php" ?>