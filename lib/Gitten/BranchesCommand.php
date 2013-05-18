<?php
/*
 * Copyright (C) 2013 Klaus Reimer <k@ailis.de>
 * See LICENSE.md for licensing information.
 */

namespace Gitten;

final class BranchesCommand extends AbstractRepoCommand
{
    protected function view()
    {
        header("Content-Type: application/json");
        echo json_encode($this->repo->getBranches());
    }
}
