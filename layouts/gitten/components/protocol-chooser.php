<? $repoProtocols = $cfg->getRepoProtocols() ?>
<? if ($repoProtocols): ?>
  <script src="scripts/gitten/ProtocolChooser.js"></script>
  <div data-component="gitten.ProtocolChooser">
    <? if (count($repoProtocols) > 1): ?>
      <button class="dropdown"><?=$repoProtocols[0]?></button>
      <ul class="popup">
        <? foreach ($repoProtocols as $protocol): ?>
         <li><a href="#" data-protocol="<?=$protocol?>"><?=$protocol?></a></li>
        <? endforeach ?>
      </ul>
    <? endif ?>
    <input class="repo-uri" type="text" readonly value="<?=$this->getRepo()->getUrl($repoProtocols[0])?>"
           <? foreach ($repoProtocols as $protocol): ?>
             data-<?=$protocol?>-uri="<?=$this->getRepo()->getUrl($protocol)?>"
           <? endforeach ?>
    />
  </div>
<? endif ?>