<?php

header("Content-Type: " . $localFile->getMimeType());
echo $localFile->getContent();
