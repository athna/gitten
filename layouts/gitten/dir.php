<? require "header.php" ?>
<? require "file-breadcrumb.php" ?>

<div class="content">
  <table class="directory">
    <? foreach ($this->getFile()->getChildren() as $file): ?>
      <tr class="<?=$file->getType()?>">

        <td class="name"><a href="<?=$file->getUrl()?>"><?=$file->getName()?></a></td>

        <? if ($cfg->hasFileSizeTreeColumn()): ?>
          <td class="size">
            <? if (!$file->isDirectory()): ?>
              <? $fileSize = $file->getSize(); ?>
              <span class="filesize"<? if ($fileSize->getSize() >= 1024): ?> title="<?=$fileSize->getLongText()?>"<? endif ?>>
                <?=$fileSize->getShortText()?>
              </span>
            <? endif ?>
          </td>
        <? endif ?>

        <? if ($cfg->hasFileSizeTreeColumn()): ?>
          <td class="lastModified">
            <?=$file->getLastModified()->getHTML()?>
          </td>
        <? endif ?>

        <? if ($cfg->hasDescriptionTreeColumn()): ?>
          <td class="description">
            <div class="shortened-text-container">
              <span class="shortened-text">
                <?=htmlspecialchars($file->getDescription())?>
              </span>
            </div>
          </td>
        <? endif ?>

      </tr>
    <? endforeach ?>
  </table>
</div>

<? include "footer.php" ?>