<?php
/*
 * Copyright (C) 2013 Klaus Reimer <k@ailis.de>
 * See LICENSE.md for licensing information.
 */

namespace Gitten;

interface Command
{
    /**
     * Executes the command.
     *
     * @param string $file
     *            The file on which to execute the command.
     * @param string[] $args
     *            The command arguments.
     * @param string[string] $params
     *            The command parameters.
     */
    function execute($file, $args, $params);
}
