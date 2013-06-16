<? $pageId = "commits" ?>
<? include "parts/header.php" ?>
<? include "parts/file-breadcrumb.php" ?>
<? include "parts/repo-nav.php" ?>

<? $page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ?>

<div class="content">
  <? include "components/rev-chooser.php"?>

  <table class="commits">
    <tr>
      <th colspan="2">Author</th>
      <th>Commit</th>
      <th>Message</th>
      <th>Date</th>
    </tr>
    <? $commits = $repo->getCommits("", 30, $page, $hasMore) ?>
    <? foreach ($commits as $commit): ?>
      <tr>
        <td class="authorAvatar">
          <?=$commit->getAuthor()->getAvatarHTML()?>
        </td>
        <td class="author">
          <?=$commit->getAuthor()->getHTML()?>
        </td>
        <td class="hash">
          <?=$commit->getHTML()?>
        </td>
        <td class="message">
          <div class="shortened-text-container">
            <span class="shortened-text">
              <?=htmlspecialchars($commit->getSubject())?>
            </span>
          </div>
        </td>
        <td class="date">
          <?=$commit->getCommitterDate()->getHTML()?>
        </td>
      </tr>
    <? endforeach ?>
  </table>

  <nav class="commits">
    <? if ($page > 1): ?>
      <a href="<?=$repo->getCommitsUrl($page - 1)?>">Prev</a>
    <? endif ?>
    <? if ($hasMore): ?>
      <a href="<?=$repo->getCommitsUrl($page + 1)?>">Next</a>
    <? endif ?>
  </nav>

</div>

<? include "parts/footer.php" ?>
