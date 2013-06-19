<?php
/*
 * Copyright (C) 2013 Klaus Reimer <k@ailis.de>
 * See LICENSE.md for licensing information.
 */

namespace Gitten;

/**
 * A commit diff.
 *
 * @author Klaus Reimer <k@ailis.de>
 */
class CommitDiff
{
    /** @var Repo The repository. */
    private $repo;

    /** @var Git The Git command line interface for the running diff command. */
    private $git;

    /** @var Commit The commit data parsed from the diff. */
    private $commit;

    /** @var CommitFile[string] The commited files. */
    private $files = array();

    /** @var string A line which was read but put back for next reader. */
    private $preReadLine = null;

    /**
     * Constructs a new commit renderer.
     *
     * @param Repo $repo
     *            The repository.
     */
    public function __construct(Repo $repo)
    {
        $this->repo = $repo;
        $this->open();
    }

    /**
     * Executes the Git command.
     */
    private function open()
    {
        $this->git = new Git($this->repo);
        $this->git->open("diff-tree", "--numstat", "--raw", "--patch",
            "--no-renames", "--diff-filter=ADM", "-l0",
            "--pretty=format:%H%x00%T%x00%P%x00%at%x00%an" .
            "%x00%ae%x00%ct%x00%cn%x00%ce%x00%s",
            $this->repo->getRevisionHash());
        $this->parseCommit();
        $this->parseFiles();
    }

    /**
     * Finishes the Git command.
     */
    private function close()
    {
        $this->git->close();
    }

    /**
     * Parses the git diff header.
     */
    private function parseCommit()
    {
        $row = $this->readLine();
        $cols = explode("\0", $row);
        $commitHash = new Hash($cols[0]);
        $treeHash = new Hash($cols[1]);
        $parentHash = new Hash($cols[2]);
        $authorDate = new DateTime($cols[3]);
        $author = new Contact($cols[4], $cols[5]);
        $committerDate = new DateTime($cols[6]);
        $committer = new Contact($cols[7], $cols[8]);
        $subject = $cols[9];
        $this->commit = new Commit($this->repo, $commitHash, $treeHash,
                $parentHash, $authorDate, $author, $committerDate,
                $committer, $subject);
    }

    /**
     * Parses the commit files from the diff output.
     */
    private function parseFiles()
    {
        $files = array();
        while (($line = $this->readLine()))
        {
            if ($line[0] == ':')
            {
                $parts = preg_split('/\s+/', substr($line, 1));
                $srcMode = new FileMode(octdec($parts[0]));
                $destMode = new FileMode(octdec($parts[1]));
                $srcHash = new Hash($parts[2]);
                $destHash = new Hash($parts[3]);
                $type = $parts[4];
                $filename = $parts[5];
                $file = $this->createFile($filename);
                $file->setRawData($type, $srcMode, $destMode, $srcHash, $destHash);
            }
            else
            {
                $parts = preg_split('/\s+/', $line);
                $additions = intval($parts[0]);
                $deletions = intval($parts[1]);
                $binary = ($parts[0] == "-") && ($parts[1] == "-");
                $filename = $parts[2];
                $file = $this->createFile($filename);
                $file->setNumStatData($additions, $deletions, $binary);
            }
        }
    }

    /**
     * Creates a new commit file if not already present.
     *
     * @param string $filename
     *            The commit file name.
     * @return CommitFile
     *            The newly created commit file or the existing one if present.
     */
    private function createFile($filename)
    {
        if (isset($this->files[$filename]))
        {
            return $this->files[$filename];
        }
        else
        {
            $file = new CommitFile($this->repo, $this->commit,
                count($this->files), $filename);
            $this->files[$filename] = $file;
            return $file;
        }
    }

    /**
     * Returns the commit.
     *
     * @return Commit
     *            The commit.
     */
    public function getCommit()
    {
        return $this->commit;
    }

    /**
     * Returns the commit files.
     *
     * @return CommitFile[string]
     *            The commit files.
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Returns only the deleted files in the commit.
     *
     * @return CommitFile[]
     *            The deleted files.
     */
    public function getDeletedFiles()
    {
        $files = array();
        foreach ($this->files as $file)
        {
            if ($file->isDeletion()) $files[] = $file;
        }
        return $files;
    }

    /**
     * Returns only the added files in the commit.
     *
     * @return CommitFile[]
     *            The added files.
     */
    public function getAddedFiles()
    {
        $files = array();
        foreach ($this->files as $file)
        {
            if ($file->isAddition()) $files[] = $file;
        }
        return $files;
    }

    /**
     * Returns only the changed files in the commit.
     *
     * @return CommitFile[]
     *            The added files.
     */
    public function getChangedFiles()
    {
        $files = array();
        foreach ($this->files as $file)
        {
            if ($file->isModification()) $files[] = $file;
        }
        return $files;
    }

    /**
     * Reads the next line from the git commands result.
     *
     * @return string
     *            The next line read from the git commands result.
     */
    private function readLine($stripEOF = true)
    {
        if (!is_null($this->preReadLine))
        {
            $line = $this->preReadLine;
            $this->preReadLine = null;
        }
        else
        {
            $line = $this->git->readLine(false);
        }
        if ($line === false) return false;
        return $stripEOF ? trim($line, "\r\n") : $line;
    }

    /**
     * Puts back a line read from the git commands result so the next read
     * uses this line instead of the next one.
     *
     * @param string $line
     *            The line to put back.
     */
    private function putBackLine($line)
    {
        $this->preReadLine = $line;
    }

    /**
     * Advances to the next file in the diff.
     *
     * @return CommitFile
     *            The next file in the diff or null if end has been reached.
     */
    public function nextFile()
    {
        while (($line = $this->readLine()) !== false)
        {
            if (!strncmp("diff ", $line, 5))
            {
                $parts = explode(" ", $line);
                $file = substr(array_pop($parts), 2);
                return $this->files[$file];
            }
        }
        return null;
    }

    /**
     * Advances to the next chunk in the diff.
     *
     * @return CommitFile
     *            The next file in the diff or null if end has been reached.
     */
    public function nextChunk()
    {
        while (($line = $this->readLine()) !== false)
        {
            if (!strncmp("@@ ", $line, 3))
            {
                preg_match("/@@ -([0-9,]+) \\+([0-9,]+) @@/",
                    $line, $matches);
                $srcRange = $matches[1];
                $destRange = $matches[2];
                $parts = explode(",", $srcRange);
                $srcStart = array_shift($parts);
                $srcLines = $parts ? array_shift($parts) : 1;
                $parts = explode(",", $destRange);
                $destStart = array_shift($parts);
                $destLines = $parts ? array_shift($parts) : 1;
                return new DiffChunk($srcStart, $srcLines, $destStart,
                    $destLines);
            }
            else if (!strncmp("diff ", $line, 5))
            {
                $this->putBackLine($line);
                break;
            }
        }
        return null;
    }

    /**
     * Advances to the next chunk line in the diff.
     *
     * @return DiffChunkLine
     *            The next chunk line in the diff or null if end of the chunk
     *            has been reached.
     */
    public function nextChunkLine()
    {
        while (($line = $this->readLine(false)) !== false)
        {
            // Put back read line and stop reading when then next chunk has
            // been found.
            if (!strncmp("@@ ", $line, 3) || !strncmp("diff ", $line, 5))
            {
                $this->putBackLine($line);
                break;
            }
            return new DiffChunkLine($line[0], substr($line, 1));
        }
        return null;
    }
}
