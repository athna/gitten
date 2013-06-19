<?php
/*
 * Copyright (C) 2013 Klaus Reimer <k@ailis.de>
 * See LICENSE.md for licensing information.
 */

namespace Gitten;

/**
 * Interface to the git command line tool.
 *
 * @author Klaus Reimer <k@ailis.de>
 */
final class Git
{
    /** @var string The executed Git command. */
    private $command;

    /** @var string The file name for recording Git error messages. */
    private $errorFile;

    /** @var resource The current Git process. */
    private $proc;

    /** @var array The Git process pipes. */
    private $pipes;

    /** @var number The start time for benchmarking Git commands. */
    private $startTime;

    /**
     * @var array The benchmark statistics of all executed Git commands in
     * this request.
     */
    private static $benchmark = array();


    /**
     * Constructs a new repository.
     *
     * @param LocalFile $directory
     *            The directory where the repository is located.
     * @param string $revision
     *            The selected revision.
     */
    public function __construct(Repo $repo, $args__)
    {
        global $cfg;

        $this->errorFile = tempnam(sys_get_temp_dir(), "gitten");
        $descriptors = array(
            0 => array("pipe", "r"),
            1 => array("pipe", "w"),
            2 => array("file", $this->errorFile, "w")
        );
        $args = array(
            $cfg->getGit(),
            "--git-dir",
            escapeshellarg($repo->getGitDirectory())
        );
        foreach (func_get_args() as $arg)
            $args[] = escapeshellarg($arg);
        $this->command = implode(" ", $args);
        $this->startTime = microtime(true);
        $this->proc = proc_open($this->command, $descriptors, $this->pipes);
        if (!is_resource($this->proc))
            throw new GitException($this->command, -1, $this->errorFile);
    }

    /**
     * Finishes the execution of the git command. Closes all file
     * descriptors and cleans up temporary files.
     */
    private function closeGit()
    {
        $result = proc_close($this->proc);
        self::$benchmark[] = array(
            "cmd" => $this->command,
            "time" => microtime(true) - $this->startTime
        );
        if ($result)
            throw new GitException($this->command, $result,
                $this->errorFile);
        unlink($this->errorFile);
    }

    /**
     * Reads a single line from the Git commands response.
     *
     * @return string
     *             The read line. False if end of response has been reached.
     */
    public function readLine()
    {
        return fgets($this->pipes[1]);
    }

    /**
     * Reads all lines from the Git command response and returns it as an
     * array of lines.
     *
     * @return string[]
     *             The Git command response as an array of lines.
     */
    public function readLines()
    {
        $lines = array();
        while (($line = $this->readLine()) !== false)
        {
            $lines[] = $line;
        }
        return $lines;
    }

    /**
     * Reads all data from the Git command response and returns it.
     *
     * @return string
     *             The Git command response.
     */
    public function read()
    {
        return stream_get_contents($this->pipes[1]);
    }

    /**
     * Reads the response line by line and passes each line to the given
     * callback function.
     *
     * @param callback $callback
     *            The callback function.
     */
    private function forEachLine($callback, $args___)
    {
        while (($line = $this->readLine()) !== false)
        {
            call_user_func($callback, $line);
        }
        $this->closeGit();
    }
}
