<nav class="repository-nav">
  <div class="content">
    <ul>
      <li<?if ($view == "tree"):?> class="active"<?endif?>><a href="<?=$file->getUrl()?>/tree">Source</a></li>
      <li<?if ($view == "commits"):?> class="active"<?endif?>><a href="<?=$file->getUrl()?>/commits">Commits</a></li>
    </ul>
  </div>
</nav>
