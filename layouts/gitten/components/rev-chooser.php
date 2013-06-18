<script src="scripts/gitten/RevChooser.js"></script>
<? $revType = $repo->getRevisionType() ?>
<div data-component="gitten.RevChooser"
     data-repo-path="<?=htmlspecialchars($localFile->getUrl())?>"
     data-repo-file="<?=htmlspecialchars($repoFile->getPath())?>"
     data-view="<?=$view?>">
  <button class="dropdown <?=$revType?>">
    <span class="rev-type"><?=$revType?></span>:
    <span class="rev-name"><?=$repo->getShortRevision()?></span>
  </button>
  <div class="popup">
    <input type="text" class="filter" placeholder="Filter branches and tags" />
    <div class="type branches<?if ($revType == "branch"): ?> active<? endif?>">
      <h3><a href="#" data-type="branches">Branches</a></h3>
      <ul data-type="branches">
      </ul>
    </div>
    <div class="type tags<?if ($revType == "tag"): ?> active<? endif?>">
      <h3><a href="#" data-type="tags">Tags</a></h3>
      <ul data-type="tags">
      </ul>
    </div>
  </div>
</div>
