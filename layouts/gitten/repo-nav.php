<nav class="repository-nav">
  <div class="content">
    <ul>
      <li<?if ($this->getViewName() == "tree"):?> class="active"<?endif?>><a href="<?=$this->getFile()->getPath()?>/tree">Source</a></li>
      <li<?if ($this->getViewName() == "commits"):?> class="active"<?endif?>><a href="<?=$this->getFile()->getPath()?>/commits">Commits</a></li>
    </ul>
  </div>
</nav>
