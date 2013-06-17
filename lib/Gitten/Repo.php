<?php
/*
 * Copyright (C) 2013 Klaus Reimer <k@ailis.de>
 * See LICENSE.md for licensing information.
 */

namespace Gitten;

/**
 * A Git repository.
 *
 * @author Klaus Reimer <k@ailis.de>
 */
final class Repo
{
    /** The directory where the repository is located. */
    private $directory;

    /** The selected revision. */
    private $revision;

    /**
     * @var string The cached type of the selected revision. Access it with
     * getRevisionType()
     */
    private $revisionType;

    /** @var string The revision resolved into a hash. */
    private $revisionHash;

    /**
     * @var string[] The cached available branches. Access it with
     * getBranches()
     */
    private $branches;

    /** @var string[] The cached available tags. Access it with getTags() */
    private $tags;

    /**
     * @var string The cached current branch. Access it with
     * getCurrentBranch()
     */
    private $currentBranch;

    /** @var string The cached description. Access it with getDescription(). */
    private $description;

    /** @var string The executed Git command. */
    private $gitCmd;

    /** @var string The file name for recording Git error messages. */
    private $gitErrorFile;

    /** @var number The start time for benchmarking Git commands. */
    private $gitStartTime;

    /** @var resource The current Git process. */
    private $gitProc;

    /** @var array The Git process pipes. */
    private $gitPipes;

    /**
     * @var array The benchmark statistics of all executed Git commands in
     * this request.
     */
    private $gitBenchmark;

    /**
     * Constructs a new repository.
     *
     * @param LocalFile $directory
     *            The directory where the repository is located.
     * @param string $revision
     *            The selected revision.
     */
    public function __construct(LocalFile $directory, $revision = null)
    {
        $this->directory = $directory;
        if (!$revision)
        {
            $revision = $this->getCurrentBranch();
        }
        if (!$revision)
        {
            $revision = $this->parseRev("HEAD");
            $this->revisionHash = $revision;
        }
        else
        {
            $this->revisionHash = $this->parseRev($revision);
        }
        $this->revision = $revision;
    }

    /**
     * Returns the directory where the repository is located.
     *
     * @return LocalFile
     *            The repository directory.
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * Returns the selected revision.
     *
     * @return string
     *             The selected revision.
     */
    public function getRevision()
    {
        return $this->revision;
    }

    /**
     * Returns the short revision. If revision is a tag or branch then it
     * is returned unchanged. If revision is a sha1 then only a 10
     * character long sha1 is returned.
     *
     * @return string
     *             The short revision name.
     */
    public function getShortRevision()
    {
        if (preg_match("/^[0-9a-f]{40}/", $this->revision))
            return substr($this->revision, 0, 10);
        else
            return $this->revision;
    }

    /**
     * Returns the revision type.
     *
     * @return string
     *            The revision type (branch, tag, tree).
     */
    public function getRevisionType()
    {
        if (!$this->revisionType)
        {
            $name = trim($this->gitString("rev-parse", "--symbolic-full-name",
                $this->revision));
            if (preg_match("/^refs\/tags\/.*/", $name))
                $this->revisionType = "tag";
            else if (preg_match("/^refs\/heads\/.*/", $name))
                $this->revisionType = "branch";
            else
                $this->revisionType = "tree";
        }
        return $this->revisionType;
    }

    /**
     * Returns the repository name.
     *
     * @return string
     *             The repository name.
     */
    public function getName()
    {
        return $this->directory->getName();
    }

    /**
     * Returns the tree URL for the specified repository file.
     *
     * @param RepoFile $repoFile
     *            The repository file.
     * @return string
     *             The repository file tree URL.
     */
    public function getFileUrl(RepoFile $repoFile)
    {
        if ($repoFile->isDirectory())
            return $this->directory->getPath() . "/tree/"
                . $this->revision . "/" . $repoFile->getPath();
        else
            return $this->directory->getPath() . "/blob/"
                . $this->revision . "/" . $repoFile->getPath();
    }

    /**
     * Returns the raw URL for the specified repository file.
     *
     * @param RepoFile $repoFile
     *            The repository file.
     * @return string
     *             The repository file raw URL.
     */
    public function getRawUrl(RepoFile $repoFile)
    {
        return $this->directory->getPath() . "/raw/"
            . $this->revision . "/" . $repoFile->getPath();
    }

    /**
     * Returns the git directory.
     *
     * @return string
     *             The git directory.
     */
    private function getGitDirectory()
    {
        $dir = $this->directory->getAbsolutePath();
        $subDir = $dir . "/.git";
        if (is_dir($subDir)) return $subDir;
        return $dir;
    }

    /**
     * Returns the repository description.
     *
     * @return string
     *            The repository description.
     */
    public function getDescription()
    {
        if (is_null($this->description))
        {
            $file = $this->getGitDirectory() . "/description";
            if (file_exists($file))
                $this->description = file_get_contents($file);
            else
              $this->description = "";
        }
        return $this->description;
    }

    /**
     * Opens git.
     *
     * @param mixed $args___
     *            Variable number of arguments to pass to git.
     * @throws GitException
     *            When command could not be executed.
     */
    private function openGit($args___)
    {
        global $cfg;

        $this->gitErrorFile = tempnam(sys_get_temp_dir(), "gitten");
        $descriptors = array(
            0 => array("pipe", "r"),
            1 => array("pipe", "w"),
            2 => array("file", $this->gitErrorFile, "w")
        );
        $args = array(
            $cfg->getGit(),
            "--git-dir",
            escapeshellarg($this->getGitDirectory())
        );
        foreach (func_get_args() as $arg)
            $args[] = escapeshellarg($arg);
        $this->gitCmd = implode(" ", $args);
        $this->gitStartTime = microtime(true);
        $this->gitProc = proc_open($this->gitCmd, $descriptors,
            $this->gitPipes);
        if (!is_resource($this->gitProc))
            throw new GitException($this->gitCmd, -1, $this->gitErrorFile);
    }

    /**
     * Closes git.
     */
    private function closeGit()
    {
        $result = proc_close($this->gitProc);
        $this->gitBenchmark[] = array(
            "cmd" => $this->gitCmd,
            "time" => microtime(true) - $this->gitStartTime
        );
        if ($result)
            throw new GitException($this->gitCmd, $result,
                $this->gitErrorFile);
        unlink($this->gitErrorFile);
    }

    /**
     * Executes a git command and returns the result as rows.
     *
     * @param string $rowDelimiter
     *            The row delimiter.
     * @param mixed $args___
     *            Variable number of git arguments.
     * @return string[]
     *            The result rows.
     */
    private function gitRows($rowDelimiter, $args___)
    {
        $args = func_get_args();
        array_shift($args);
        call_user_func_array(array($this, "openGit"), $args);
        $data = stream_get_contents($this->gitPipes[1]);
        $this->closeGit();
        $rows = explode($rowDelimiter, $data);
        return $rows;
    }

    /**
     * Executes a git command and returns the result as a string.
     *
     * @param mixed $args___
     *            Variable number of git arguments.
     * @return string
     *            The result string.
     */
    private function gitString($args___)
    {
        $args = func_get_args();
        call_user_func_array(array($this, "openGit"), $args);
        $data = stream_get_contents($this->gitPipes[1]);
        $this->closeGit();
        return $data;
    }

    /**
     * Executes a git command and passes the returned lines one
     * by one to the specified callback function.
     *
     * @param callback $callback
     *            The callback function.
     * @param mixed $args___
     *            Variable number of git arguments.
     */
    private function gitForEachLine($callback, $args___)
    {
        $args = func_get_args();
        array_shift($args);
        call_user_func_array(array($this, "openGit"), $args);
        while ($line = fgets($this->gitPipes[1]))
        {
            call_user_func($callback, $line);
        }
        $this->closeGit();
    }

    /**
     * Returns all children of the specified directory.
     *
     * @param RepoFile $directory
     *            The directory for which to return the children.
     * @return RepoFile[]
     *            The children. May be empty.
     */
    public function getChildren(RepoFile $directory)
    {
        $children = array();
        $path = $directory->isRoot() ? "" : ($directory->getPath() . "/");
        $repo = $this;
        $this->gitForEachLine(function($line) use ($path, &$children, $repo)
        {
            $columns = preg_split('/\s+/', trim($line), 5);
            $mode = new FileMode(octdec($columns[0]));
            $type = $columns[1];
            $size = new FileSize($columns[3] == "-" ? 0 : intval($columns[3]));
            $localFile = basename($columns[4]);
            $children[] = new RepoFile($repo, "$path$localFile",
                $type == "blob" ? "file" : "directory", $size, $mode);
        }, "ls-tree", "-l", $this->revisionHash, $directory->getPath() . "/");
        usort($children, function(RepoFile $a, RepoFile $b) {
            if ($a->isDirectory() && !$b->isDirectory()) return -1;
            if (!$a->isDirectory() && $b->isDirectory()) return 1;
            return $a->getName() > $b->getName() ? 1 : -1;
        });
        return $children;
    }


    /**
     * Returns a specific file from the repository.
     *
     * @param string $path
     *            The path to the file.
     * @return RepoFile
     *            The file or null if not found.
     */
    public function getFile($path)
    {
        $path = rtrim($path, "/");
        $line = $this->gitString("ls-tree", "-l", $this->revisionHash, $path);
        if (!$line) return null;
        $columns = preg_split('/\s+/', trim($line), 5);
        $mode = new FileMode(octdec($columns[0]));
        $type = $columns[1];
        $size = new FileSize($columns[3] == "-" ? 0 : intval($columns[3]));
        $file = basename($columns[4]);
        return new RepoFile($this, $file,
            $type == "blob" ? "file" : "directory", $size, $mode);
    }

    /**
     * Returns the current branch.
     *
     * @return string
     *            The current branch. Null if there is no branch.
     */
    public function getCurrentBranch()
    {
        $this->readBranches();
        return $this->currentBranch;
    }

    /**
     * Returns the list of branches.
     *
     * @return string[]
     *            The branches.
     */
    public function getBranches()
    {
        $this->readBranches();
        return $this->branches;
    }

    /**
     * Reads the available branches (and the current branch) once and caches
     * them for this request.
     */
    private function readBranches()
    {
        // Do nothing if already read
        if (!is_null($this->branches)) return;

        $this->currentBranch = NULL;
        $rows = $this->gitRows("\n", "branch");
        $this->branches = array();
        foreach ($rows as $row)
        {
            if (!$row) continue;
            $branch = substr($row, 2);
            if ($branch == "(no branch)") continue;
            $this->branches[] = $branch;
            if (!$this->currentBranch || $row[0] == "*")
                $this->currentBranch = $branch;
        }
    }

    /**
     * Returns the list of tags.
     *
     * @return string[]
     *            The tags.
     */
    public function getTags()
    {
        if (!is_null($this->tags)) return $this->tags;

        $rows = $this->gitRows("\n", "for-each-ref", "--sort=taggerdate",
                "--format=%(refname:short)", "refs/tags");
        $tags = array();
        foreach ($rows as $row)
        {
            if (!$row) continue;
            array_unshift($tags, $row);
        }
        $this->tags = $tags;
        return $tags;
    }

    /**
     * Parses the specified revision and returns the real revision hash if
     * possible.
     *
     * @param string $revision
     *            The revision, branch name, tag name, whatever.
     * @return string
     *            The actual revision hash.
     */
    private function parseRev($revision)
    {
        return trim($this->gitString("rev-parse", "-q", "--verify", $revision));
    }

    /**
     * Returns the commits for the specified path.
     *
     * @param RepFile $repoFile
     *            Optional repository file. Defaults to root directory.
     * @param int $number
     *            Optional number of commits to return. Defaults to 35.
     * @param int $page
     *            The page to display. Defaults to 0.
     * @return Commit[]
     *            The commits.
     */
    public function getCommits(RepoFile $repoFile = null, $number = 35,
        $page = 1, &$hasMore = null)
    {
        $commits = array();
        $repo = $this;
        $this->gitForEachLine(function($row) use (&$commits, &$repo)
        {
            $cols = explode("\0", $row);
            $commitHash = new Hash($cols[0]);
            $treeHash = new Hash($cols[1]);
            $parentHash = new Hash($cols[2]);
            $authorDate = new DateTime($cols[3]);
            $author = new Contact($cols[4], $cols[5]);
            $committerDate = new DateTime($cols[6]);
            $committer = new Contact($cols[7], $cols[8]);
            $subject = $cols[9];
            $commits[] = new Commit($repo, $commitHash, $treeHash,
                    $parentHash, $authorDate, $author, $committerDate,
                    $committer, $subject);
        }, "log", "-n", $number + 1,
        "--skip", max(0, $page - 1) * $number,
        "--format=format:%H%x00%T%x00%P%x00%at%x00%an%x00%ae%x00%ct%x00%cn%x00%ce%x00%s",
        $this->revisionHash, "--", $repoFile->getPath());
        if (count($commits) > $number)
        {
            array_pop($commits);
            $hasMore = true;
        } else $hasMore = false;
        return $commits;
    }

    /**
     * Returns the filename for the cache with the specified key.
     *
     * @param string $key
     *            The cache key.
     * @return string
     *            The cache file name.
     */
    private function getCacheFile($key)
    {
        global $cfg;

        $cacheKey = sha1($key);
        $cacheDir = $cfg->getCacheDir() . "/" . substr($cacheKey, 0, 2);
        $cacheFile = sprintf($cacheDir . "/" . substr($cacheKey, 2));
        return $cacheFile;
    }

    /**
     * Reads data from the cache with the specified key.
     *
     * @param string $key
     *            The cache key.
     * @return mixed
     *            The cached object or null if not found.
     */
    private function readCache($key)
    {
        global $cfg;

        if (!$cfg->getCacheDir()) return null;
        $cacheFile = $this->getCacheFile($key);
        if (!file_exists($cacheFile)) return false;
        return unserialize(file_get_contents($cacheFile));
    }

    /**
     * Writes data to the cache.
     *
     * @param string $key
     *            The cache key.
     * @param mixed $data
     *            The data to cache.
     * @return mixed
     *            The cached object or null if not found.
     */
    private function writeCache($key, $data)
    {
        global $cfg;

        if (!$cfg->getCacheDir()) return;
        $cacheFile = $this->getCacheFile($key);
        $cacheDir = dirname($cacheFile);
        if  (!is_dir($cacheDir)) mkdir($cacheDir, 0777, true);
        file_put_contents($cacheFile, serialize($data));
    }

    /**
     * Returns the last commit for the specified file.
     *
     * @param RepoFile $localFile
     *            The repository file.
     * @return Commit
     *            The last commit.
     */
    public function getLastCommit(RepoFile $localFile)
    {
        $cacheKey = "commit:" . $this->revisionHash . ":" . $localFile->getPath();
        $commit = $this->readCache($cacheKey);
        if ($commit) return $commit;
        $commits = $this->getCommits($localFile,  1);
        $commit = $commits[0];
        $this->writeCache($cacheKey, $commit);
        return $commit;
    }

    /**
     * Returns the raw content of a file.
     *
     * @param RepoFile $localFile
     *            The file to read.
     * @param number $lineCount
     *            Optional variable reference into which the number of read
     *            lines is written.
     * @return string
     *            The raw file content.
     */
    public function readFile(RepoFile $localFile, &$lineCount = null)
    {
        $result = "";
        $lines = 0;
        $this->gitForEachLine(function($row) use (&$result, &$lines)
        {
            $lines++;
            $result .= $row;
        }, "show", $this->revisionHash . ":" .$localFile->getPath());
        if (func_num_args() == 2) $lineCount = $lines;
        return $result;
    }

    /**
     * Returns the repository URL for the specified protocol.
     *
     * @param string $protocol
     *            The protocol
     * @return string
     *            The repository URL.
     */
    public function getUrl($protocol)
    {
        global $cfg;

        return $cfg->getRepoBaseUrl($protocol) . "/" . $this->directory->getPath();
    }

    /**
     * Returns the commit URL for the specified commit.
     *
     * @param Commit $commit
     *            The commit.
     * @return string
     *             The commit URL.
     */
    public function getCommitUrl(Commit $commit)
    {
        return $this->directory->getPath() . "/commit/" . $commit->getCommitHash();
    }

    /**
     * Returns the commits history URL for the specified repository file.
     *
     * @param RepoFile $repoFile
     *             The repository file. null for root.
     * @param int $page
     *             The page number. Defaults to 1.
     * @return string
     *             The commits listing URL.
     */
    public function getCommitsUrl(RepoFile $repoFile = null, $page = 1)
    {
        $url = $this->directory->getPath() . "/commits/" . $this->revision
            . ($repoFile ? "/" . $repoFile->getPath() : "");
        if ($page > 1) $url .= "?page=" . $page;
        return $url;
    }
}
