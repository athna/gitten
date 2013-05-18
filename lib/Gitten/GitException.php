<?php
/*
 * Copyright (C) 2013 Klaus Reimer <k@ailis.de>
 * See LICENSE.md for licensing information.
 */

namespace Gitten;

/**
 * Thrown when a Git command execution fails.
 *
 * @author Klaus Reimer <k@ailis.de>
 */
class GitException extends \Exception
{
    /**
     * Constructs the exception.
     *
     * @param string $cmd
     *            The executed command.
     * @param int $errorCode
     *            The returned error code.
     * @param string $errorFile
     *            The file containing the error message.
     */
    public function __construct($cmd, $errorCode, $errorFile)
    {
        $errorMessage = file_get_contents($errorFile);
        unlink($errorFile);
        $message = sprintf("Git execution failed with error code %s.\n" .
            "Executed command: %s\nError message:\n%s", $errorCode, $cmd,
            $errorMessage);
        parent::__construct($message, $errorCode);
    }
}
