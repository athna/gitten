<? include "header.php" ?>
<? include "file-breadcrumb.php" ?>
<? define("REPO_NAV", "source"); include "repo-nav.php" ?>
<? include "repo-breadcrumb.php" ?>

<div class="content">
  <table class="directory">
    <? foreach ($this->getRepoFile()->getChildren() as $file): ?>
      <tr class="<?=$file->getType()?>">
        <td class="name"><a href="<?=$file->getUrl()?>"><?=htmlspecialchars($file->getName())?></a></td>

        <? if ($cfg->hasFileSizeTreeColumn()): ?>
          <td class="size">
            <? if (!$file->isDirectory()): ?>
              <? $fileSize = $file->getSize(); ?>
              <span class="filesize"<? if ($fileSize->getSize() >= 1024): ?> title="<?=$fileSize->getLongText()?>"<? endif ?>>
                <?=$fileSize->getShortText()?>
              </span>
            <? endif?>
          </td>
        <? endif ?>

        <? if ($cfg->hasLastModifiedTreeColumn()): ?>
          <td class="lastModified">
            <?=$file->getLastCommit()->getCommitterDate()->getHTML()?>
          </td>
        <? endif ?>

        <? if ($cfg->hasAuthorAvatarTreeColumn()): ?>
          <td class="authorAvatar">
            <?=$file->getLastCommit()->getAuthor()->getAvatarHTML()?>
          </td>
        <? endif ?>

        <? if ($cfg->hasAuthorTreeColumn()): ?>
          <td class="author">
            <?=$file->getLastCommit()->getAuthor()->getHTML()?>
          </td>
        <? endif ?>

        <? if ($cfg->hasMessageTreeColumn()): ?>
          <td class="message">
            <div class="shortened-text-container">
              <span class="shortened-text">
                <?=htmlspecialchars($file->getLastCommit()->getSubject())?>
              </span>
            </div>
          </td>
        <? endif ?>

      </tr>
    <? endforeach ?>
  </table>
</div>

<? include "footer.php" ?>