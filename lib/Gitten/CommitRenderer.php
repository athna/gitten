<?php
/*
 * Copyright (C) 2013 Klaus Reimer <k@ailis.de>
 * See LICENSE.md for licensing information.
 */

namespace Gitten;

/**
 * Renders a commit.
 *
 * @author Klaus Reimer <k@ailis.de>
 */
class CommitRenderer
{
    /** The repository. */
    private $repo;

    /** The current parse mode. */
    private $mode;

    /** The commit files. */
    private $files;

    /** The current filename while parsing the patches. */
    private $filename;

    /** The commit hash. */
    private $commitHash;

    /** The parent commit hash. */
    private $parentHash;

    /** If diff parser is currently in header. */
    private $inHeader;

    /** The current source line while parsing file diff. */
    private $srcLine;

    /** The current source line while parsing file diff. */
    private $destLine;

    /** The source lines. */
    private $srcLines;

    /** The destination lines. */
    private $destLines;

    /**
     * Constructs a new commit renderer.
     *
     * @param Repo $repo
     *            The repository.
     */
    public function __construct(Repo $repo, $commitHash)
    {
        $this->repo = $repo;
        $this->reset();
        $this->commitHash = $commitHash;
    }

    /**
     * Returns the repository.
     *
     * @return Repository
     *            The repository.
     */
    public function getRepo()
    {
        return $this->repo;
    }

    /**
     * Returns the commit hash.
     *
     * @return string
     *            The commit hash.
     */
    public function getCommitHash()
    {
        return $this->commitHash;
    }

    /**
     * Returns the parent hash.
     *
     * @return string
     *            The parent hash.
     */
    public function getParentHash()
    {
        return $this->parentHash;
    }

    /**
     * Renders the specified commit.
     *
     * @param string $hash
     *            The commit hash.
     */
    public function finish()
    {
//        $this->repo->gitForEachLine(array($this, "processLine"), "diff-tree",
  //          "--numstat", "--raw", "--patch", "--no-renames",
    //        "--pretty=format:%P", $hash);
        if ($this->filename) $this->renderFileFooter();
    }

    /**
     * Resets the renderer.
     */
    private function reset()
    {
        $this->mode = 0;
        $this->files = array();
        $this->commitHash = NULL;
        $this->parentHash = NULL;
        $this->inHeader = false;
    }

    /**
     * Processes a line of the Git response.
     *
     * @param string $line
     *            The Git response line to process.
     */
    public function processLine($line)
    {
        switch ($this->mode)
        {
            case 0:
                // We don't care about line endings.
                $line = trim($line);

                // When line is empty then render the file overview and
                // switch over to diff parsing.
                if ($line == "")
                {
                    $this->mode = 1;
                    $this->renderFileList();
                    break;
                }

                // Parse the line
                if (!$this->parentHash)
                    $this->parentHash = $line;
                else if ($line[0] == ":")
                    $this->processRawLine($line);
                else
                    $this->processNumStatLine($line);
                break;

            case 1:
                $this->processPatchLine($line);
                break;
        }
    }

    /**
     * Renders the files in the commit.
     */
    private function renderFileList()
    {
        $files = $this->files;
        $numFiles = count($files);
        $additions = 0;
        $deletions = 0;
        $maxModifications = 0;
        foreach ($files as $file)
        {
            $additions += $file->getAdditions();
            $deletions += $file->getDeletions();
            $maxModifications = max($maxModifications,
                $file->getModifications());
        }
        $modifications = $additions + $deletions;
        include "parts/commit/files.php";
    }

    /**
     * Creates a commit file for the specified filename. If this file
     * was already created before then the already created file is
     * returned instead.
     *
     * @param string $filename
     *            The filename.
     * @return CommitFile
     *            The file.
     */
    private function createFile($filename)
    {
        if (isset($this->files[$filename]))
            return $this->files[$filename];
        else
        {
            $file = new CommitFile($this, count($this->files),
                $filename);
            $this->files[$filename] = $file;
            return $file;
        }
    }

    /**
     * Processes a line from the raw output.
     *
     * @param string $line
     *            The line to process.
     */
    private function processRawLine($line)
    {
        $parts = preg_split('/\s+/', substr($line, 1));
        $srcMode = $parts[0];
        $destMode = $parts[1];
        $srcHash = $parts[2];
        $destHash = $parts[3];
        $type = $parts[4];
        $filename = $parts[5];
        $file = $this->createFile($filename);
        $file->setRawData($type, $srcMode, $destMode, $srcHash, $destHash);
    }

    /**
     * Processes a line from the numstat output.
     *
     * @param string $line
     *            The line to process.
     */
    private function processNumStatLine($line)
    {
        $parts = preg_split('/\s+/', $line);
        $additions = intval($parts[0]);
        $deletions = intval($parts[1]);
        $binary = ($parts[0] == "-") && ($parts[1] == "-");
        $filename = $parts[2];
        $file = $this->createFile($filename);
        $file->setNumStatData($additions, $deletions, $binary);
    }

    /**
     * Processes a line from the patch output.
     *
     * @param string $line
     *            The line to process.
     */
    private function processPatchLine($line)
    {
        if (strpos($line, "diff") === 0)
        {
            $this->processPatchDiffLine($line);
            $this->inHeader = true;
            $this->sourceLines = array();
            $this->destLines = array();
            $this->maxSourceLine = 0;
            $this->maxDestLine = 0;
        }
        else if ($line[0] == "@")
        {
            $this->processPatchChunkLine($line);
            $this->inHeader = false;
        }
        else if (!$this->inHeader)
        {
            if ($line[0] == " ")
                $this->processPatchUnchangedLine($line);
            else if ($line[0] == "+")
                $this->processPatchAddedLine($line);
            else if ($line[0] == "-")
                $this->processPatchRemovedLine($line);
        }
    }

    /**
     * Processes a diff line from the patch output.
     *
     * @param string $line
     *            The line to process.
     */
    private function processPatchDiffLine($line)
    {
        $parts = explode(" ", $line);
        if ($this->filename)
            $this->renderFileFooter();
        $this->filename = substr($parts[2], 2);
        $this->renderFileHeader();
    }

    /**
     * Processes a chunk line from the patch output.
     *
     * @param string $line
     *            The line to process.
     */
    private function processPatchChunkLine($line)
    {
        if (!preg_match('/@@ -([0-9,]+) \+([0-9,]+) @@/',
                $line, $result))
            throw new \RuntimeException("Unable to parse chunk line: $line");
        $srcRange = $result[1];
        $destRange = $result[2];
        $parts = explode(",", $srcRange);
        $srcStart = array_shift($parts);
        $srcLines = $parts ? array_shift($parts) : 1;
        $parts = explode(",", $destRange);
        $destStart = array_shift($parts);
        $destLines = $parts ? array_shift($parts) : 1;
        $this->maxSourceLine = max($this->maxSourceLine, $srcStart +
            $srcLines - 1);
        $this->maxDestLine = max($this->maxDestLine, $destStart +
            $destLines - 1);
        $this->sourceLine = $srcStart;
        $this->destLine = $destStart;
        $this->sourceLines[] = "@@";
        $this->destLines[] = "@@";
        printf("<span class=\"line chunk\">%s</span>", htmlspecialchars($line));
    }

    /**
     * Processes an unchanged line from the patch output.
     *
     * @param string $line
     *            The line to process.
     */
    private function processPatchUnchangedLine($line)
    {
        printf("<span class=\"line\">%s</span>", htmlspecialchars($line));
        $this->sourceLines[] = $this->sourceLine++;
        $this->destLines[] = $this->destLine++;
    }

    /**
     * Processes an added line from the patch output.
     *
     * @param string $line
     *            The line to process.
     */
    private function processPatchAddedLine($line)
    {
        printf("<span class=\"line add\">%s</span>", htmlspecialchars($line));
        $this->sourceLines[] = "";
        $this->destLines[] = $this->destLine++;
    }

    /**
     * Processes a deleted line from the patch output.
     *
     * @param string $line
     *            The line to process.
     */
    private function processPatchRemovedLine($line)
    {
        printf("<span class=\"line delete\">%s</span>", htmlspecialchars($line));
        $this->sourceLines[] = $this->sourceLine++;
        $this->destLines[] = "";
    }

    /**
     * Renders the file header.
     */
    private function renderFileHeader()
    {
        $file = $this->files[$this->filename];
        include "parts/commit/fileheader.php";
    }

    /**
     * Renders the file footer.
     */
    private function renderFileFooter()
    {
        $file = $this->files[$this->filename];
        $sourceLines = $this->sourceLines;
        $destLines = $this->destLines;
        $maxSourceLine = $this->maxSourceLine;
        $maxDestLine = $this->maxDestLine;
        $parentBlobUrl = $this->getParentBlobUrl();
        $commitBlobUrl = $this->getCommitBlobUrl();
        include "parts/commit/filefooter.php";
    }

    /**
     * Returns the URL to the commit blob.
     *
     * @return string
     *             The commit blob url or NULL if blob was deleted.
     */
    public function getCommitBlobUrl()
    {
    	return "Commit blob URL";
    	/*
        $revision = $this->commitHash;
        $path = $this->filename;
        $repoUrl = $this->repo->getUrl();
        return "$repoUrl/blob/$revision/$path";*/
    }

    /**
     * Returns the URL to the parent blob.
     *
     * @return string
     *             The parent blob url or NULL if blob was added.
     */
    public function getParentBlobUrl()
    {
    	return "Parent blob url";
/*        $revision = $this->parentHash;
        $path = $this->filename;
        $repoUrl = $this->repo->getUrl();
        return "$repoUrl/blob/$revision/$path";*/
    }
}
