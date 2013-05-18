<?php

header("Content-Type: " . $repoFile->getMimeType());
echo $repoFile->getContent();
