<? include "parts/header.php" ?>
<? include "parts/file-breadcrumb.php" ?>
<? include "parts/repo-nav.php" ?>
<? include "parts/repo-breadcrumb.php" ?>

<div class="content">
  <table class="directory">
    <? foreach ($repoFile->getChildren() as $child): ?>
      <tr class="<?=$child->getType()?>">
        <td class="name"><a href="<?=$child->getUrl()?>"><?=htmlspecialchars($child->getName())?></a></td>

        <? if ($cfg->hasFileSizeTreeColumn()): ?>
          <td class="size">
            <? if (!$child->isDirectory()): ?>
              <? $childSize = $child->getSize(); ?>
              <span class="filesize"<? if ($childSize->getSize() >= 1024): ?> title="<?=$childSize->getLongText()?>"<? endif ?>>
                <?=$childSize->getShortText()?>
              </span>
            <? endif?>
          </td>
        <? endif ?>

        <? if ($cfg->hasLastModifiedTreeColumn()): ?>
          <td class="lastModified">
            <?=$child->getLastCommit()->getCommitterDate()->getHTML()?>
          </td>
        <? endif ?>

        <? if ($cfg->hasAuthorAvatarTreeColumn()): ?>
          <td class="authorAvatar">
            <?=$child->getLastCommit()->getAuthor()->getAvatarHTML()?>
          </td>
        <? endif ?>

        <? if ($cfg->hasAuthorTreeColumn()): ?>
          <td class="author">
            <?=$child->getLastCommit()->getAuthor()->getHTML()?>
          </td>
        <? endif ?>

        <? if ($cfg->hasMessageTreeColumn()): ?>
          <td class="message">
            <div class="shortened-text-container">
              <span class="shortened-text">
                <?=htmlspecialchars($child->getLastCommit()->getSubject())?>
              </span>
            </div>
          </td>
        <? endif ?>

      </tr>
    <? endforeach ?>
  </table>

  <? $readme = $repoFile->getReadmeHTML() ?>
  <? if ($readme): ?>
    <div class="textfile">
      <?=$readme?>
    </div>
  <? endif ?>

</div>

<? include "parts/footer.php" ?>