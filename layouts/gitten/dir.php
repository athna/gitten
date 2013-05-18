<? require "parts/header.php" ?>
<? require "parts/file-breadcrumb.php" ?>

<div class="content">
  <table class="directory">
    <? foreach ($localFile->getChildren() as $child): ?>
      <tr class="<?=$child->getType()?>">

        <td class="name"><a href="<?=$child->getUrl()?>"><?=$child->getName()?></a></td>

        <? if ($cfg->hasFileSizeTreeColumn()): ?>
          <td class="size">
            <? if (!$child->isDirectory()): ?>
              <? $childSize = $child->getSize(); ?>
              <span class="filesize"<? if ($childSize->getSize() >= 1024): ?> title="<?=$childSize->getLongText()?>"<? endif ?>>
                <?=$childSize->getShortText()?>
              </span>
            <? endif ?>
          </td>
        <? endif ?>

        <? if ($cfg->hasFileSizeTreeColumn()): ?>
          <td class="lastModified">
            <?=$child->getLastModified()->getHTML()?>
          </td>
        <? endif ?>

        <? if ($cfg->hasDescriptionTreeColumn()): ?>
          <td class="description">
            <div class="shortened-text-container">
              <span class="shortened-text">
                <?=htmlspecialchars($child->getDescription())?>
              </span>
            </div>
          </td>
        <? endif ?>

      </tr>
    <? endforeach ?>
  </table>
</div>

<? include "parts/footer.php" ?>