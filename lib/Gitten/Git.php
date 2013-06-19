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
	/** @var Repo The repository. */
	private $repo;

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
    public function __construct(Repo $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Opens the git command with the specified arguments.
     *
     * @param mixed $args__
     *            The git command arguments.
     */
    public function open($args__)
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
            escapeshellarg($this->repo->getGitDirectory())
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
    private function close()
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
     * @param boolean stripEOL
     *             If EOL characters should be stripped. Defaults to true.
     * @return string
     *             The read line. False if end of response has been reached.
     */
    public function readLine($stripEOL = true)
    {
        $line = fgets($this->pipes[1]);
        if ($line === false) return false;
        return $stripEOL ? trim($line, "\n\r") : $line;
    }

    /**
     * Reads all lines from the Git command response and returns it as an
     * array of lines.
     *
     * @param boolean stripEOL
     *             If EOL characters should be stripped. Defaults to true.
     * @return string[]
     *             The Git command response as an array of lines.
     */
    public function readLines($stripEOL = true)
    {
        $lines = array();
        while (($line = $this->readLine($stripEOL)) !== false)
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
     * @param boolean stripEOL
     *             If EOL characters should be stripped. Defaults to true.
     * @param callback $callback
     *            The callback function. The line with stripped EOF characters
     *            is passed to it as first argument. The unstripped line is
     *            passed as second argument.
     */
    public function forEachLine($callback)
    {
        while (($line = $this->readLine(false)) !== false)
        {
            call_user_func($callback, trim($line, "\r\n"), $line);
        }
    }

    /**
     * Short form for executing a Git command and returning the result as a
     * string. This method automatically opens and closes the Git command line
     * interface.
     *
     * @param Repo $repo
     *            The git repository.
     * @param mixed $args___
     *            The git arguments.
     * @return string
     *            The git command result.
     */
    public static function exec(Repo $repo, $args___)
    {
    	$git = new Git($repo);
    	$args = func_get_args();
    	array_shift($args);
    	call_user_func_array(array($git, "open"), $args);
    	$result = $git->read();
    	$git->close();
    	return $result;
    }

    /**
     * Short form for executing a Git command and returning the result as a
     * list of lines. This method automatically opens and closes the Git
     * command line interface.
     *
     * @param Repo $repo
     *            The git repository.
     * @param mixed $args___
     *            The git arguments.
     * @return string[]
     *            The git command result as a list of lines.
     */
    public static function execForLines(Repo $repo, $args___)
    {
    	$git = new Git($repo);
    	$args = func_get_args();
    	array_shift($args);
    	call_user_func_array(array($git, "open"), $args);
    	$result = $git->readLines();
    	$git->close();
    	return $result;
    }

    /**
     * Short form for executing a Git command and passing each line of the result
     * to a callback function. This method automatically opens and closes the Git
     * command line interface.
     *
     * @param Repo $repo
     *            The git repository.
     * @param callback $callback
     *            The callback function. The line with stripped EOF characters
     *            is passed to it as first argument. The unstripped line is
     *            passed as second argument.
     * @param mixed $args___
     *            The git arguments.
     */
    public static function execForEachLine(Repo $repo, $callback, $args___)
    {
    	$git = new Git($repo);
    	$args = func_get_args();
    	array_shift($args);
    	array_shift($args);
    	call_user_func_array(array($git, "open"), $args);
    	$git->forEachLine($callback);
    	$git->close();
    }
}
