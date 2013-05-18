<?php
/*
 * Copyright (C) 2013 Klaus Reimer <k@ailis.de>
 * See LICENSE.md for licensing information.
 */

namespace Gitten;

final class Gitten
{
    /**
     * Execute the application.
     */
    private function execute()
    {
        // Parse path info
        $pathInfo = trim(isset($_SERVER["PATH_INFO"]) ? $_SERVER["PATH_INFO"]
            : "", "/");
        $parts = explode("/", $pathInfo);

        // Find the file/repo on which to execute the command
        $repo = null;
        $file = new File();
        for ($i = 0, $max = count($parts); $i < $max; $i += 1)
        {
            $part = $parts[$i];
            if (!$part) break;
            $file = $file->getChild($part);
            if ($file->isRepository())
            {
                break;
            }
        }

        // Determine the command
        $i += 1;
        if ($i < $max)
        {
            $cmd = $parts[$i];
        }
        else if ($file->isRepository())
        {
            $cmd = "tree";
        }
        else if ($file->isDirectory())
        {
            $cmd = "dir";
        }
        else
        {
            $cmd = "file";
        }

        // Determine the arguments
        $i += 1;
        $args = array_slice($parts, $i);

        $this->executeCommand($cmd, $file, $args, $_REQUEST);
    }

    /**
     * Executes a command.
     *
     * @param string $cmd
     *            The command to execute.
     * @param string $file
     *            The file on which to execute the command.
     * @param string[] $args
     *            The command arguments.
     * @param string[string] $params
     *            The command parameters.
     */
    private function executeCommand($cmd, $file, $args, $params)
    {
        $className = $this->getCommandClassName($cmd);
        $command = new $className();
        $command->execute($file, $args, $params);
    }

    /**
     * Converts the specified command name into a command class name.
     *
     * @param string $cmd
     *            The command name.
     * @return string
     *            The class name.
     */
    private function getCommandClassName($cmd)
    {
        $name = "";
        $parts = explode("_", $cmd);
        foreach ($parts as $part)
        {
            $name .= ucfirst($part);
        }
        return "Gitten\\" . $name . "Command";
    }

    /**
     * Creates the application and runs it.
     */
    public static function run()
    {
        $gitten = new Gitten();
        $gitten->execute();
    }
}
