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
    public function execute()
    {
    	global $cfg;
    	
        // Parse path info
        $pathInfo = trim(isset($_SERVER["PATH_INFO"]) ? $_SERVER["PATH_INFO"]
            : "", "/");
        $parts = explode("/", $pathInfo);

        // Find the file/repo on which to execute the command
        $repo = null;
        $localFile = new LocalFile();
        for ($i = 0, $max = count($parts); $i < $max; $i += 1)
        {
            $part = $parts[$i];
            if (!$part) break;
            $localFile = $localFile->getChild($part);
            if ($localFile->isRepository())
            {
                break;
            }
        }

        // Determine the view
        $i += 1;
        if ($i < $max)
        {
            $view = $parts[$i];
        }
        else if ($localFile->isRepository())
        {
            $view = "tree";
        }
        else if ($localFile->isDirectory())
        {
            $view = "dir";
        }
        else
        {
            $view = "file";
        }

        // Determine the arguments
        $i += 1;
        $args = array_slice($parts, $i);

        // Process arguments if file is a repository
        if ($localFile->isRepository())
        {
            if (count($args))
                $revision = array_shift($args);
            else
                $revision = null;
            $repo = new Repo($localFile, $revision);
            $path = trim(implode("/", $args), "/");
            if ($path == "")
                $repoFile = new RepoFile($repo);
            else
                $repoFile = new RepoFile($repo, $path);
        }
        else
        {
            $repo = null;
            $repoFile = null;
        }

        // Get request parameters
        $params = $_REQUEST;

        // Display the view (in an anonymous function to get a clean local
        // variable scope and to prevent access to the current object)
        $this->view($view, $localFile, $repo, $repoFile, $args, $params, $cfg);
    }

    /**
     * Forwards to a view.
     *
     * @param string $view
     *            The view name.
     * @param LocalFile $localFile
     *            The physical file/directory.
     * @param Repo $repo
     *            The repository. Null if none.
     * @param RepFile $repoFile
     *            The repository file. Null if not a repository.
     * @param string[] args
     *            The request arguments.
     * @param string[string] params
     *            The request parameters.
     * @param Config $cfg
     *            The configuration.
     */
    private function view($view, LocalFile $localFile, $repo, $repoFile,
        $args, $params, $cfg)
    {
        require($view . ".php");
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
