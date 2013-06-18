<nav class="repository-nav">
  <div class="content">
    <ul>
      <li<?if ($view == "blob" || $view == "tree"):?> class="active"<?endif?>><a href="<?=$repoFile->getUrl()?>">Source</a></li>
      <li<?if ($view == "commits"):?> class="active"<?endif?>><a href="<?=$repoFile->getCommitsUrl()?>">Commits</a></li>
    </ul>
  </div>
</nav>
