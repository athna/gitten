<?php
/*
 * Copyright (C) 2013 Klaus Reimer <k@ailis.de>
 * See LICENSE.md for licensing information.
 */

namespace Gitten;

/**
 * Info about a commit.
 *
 * @author Klaus Reimer <k@ailis.de>
 */
final class Config
{
    /** The singleton instance of the configuration. */
    public static $instance;

    /** The configuration values. */
    private $values = array(
        "git" => "git",
        "repoBase" => "/git",
        "layout" => "gitten",
        "theme" => "gitten",
        "cacheDir" => null,
        "repoBaseUrls" => array(
        ),
        "treeColumns" => array(
            "fileSize" => true,
            "lastModified" => false,
            "author" => false,
            "authorAvatar" => false,
            "message" => false,
            "description" => true
        )
    );

    /**
     * Constructs a new configuration instance. This is private because this
     * class uses the singleton pattern.
     */
    private function __construct()
    {
        $this->values["repoBaseUrls"]["ssh"]= "username@"
            . $_SERVER["HTTP_HOST"] . "/git";
        $this->loadConfig();
    }

    /**
     * Loads the configuration.
     */
    private function loadConfig()
    {
        $file = $this->findConfig();
        $values = parse_ini_file($file, true);
        $this->values = array_merge($this->values, $values);
    }

    /**
     * Searches the configuration file.
     *
     * @return string
     *             The configuration file location or null if no config file
     *             found.
     */
    private function findConfig()
    {
        // Search for GITTEN_CONFIG
        $GITTEN_CONFIG = getenv("GITTEN_CONFIG");
        if ($GITTEN_CONFIG && is_file($GITTEN_CONFIG)) return $GITTEN_CONFIG;

        // Search for /etc/gitten.ini
        $file = "/etc/gitten.ini";
        if (is_file($file)) return $file;

        // Search the custom gitten config in the default location
        $file = "config/gitten-local.ini";
        if (is_file($file)) return $file;

        // Search in default location
        $file = "config/gitten.ini";
        if (is_file($file)) return $file;

        // No config found
        return null;
    }

    public static function getInstance()
    {
        if (!self::$instance) self::$instance = new Config();
        return self::$instance;
    }

    /**
     * Returns the git executeable path.
     *
     * @return string
     *            The git executable path.
     */
    public function getGit()
    {
        return $this->values["git"];
    }

    /**
     * Returns the repositories base directory.
     *
     * @return string
     *            The repositories base directory.
     */
    public function getRepoBase()
    {
        return $this->values["repoBase"];
    }

    /**
     * Returns the layout to use for the HTML views.
     *
     * @return string
     *            The HTML views layout.
     */
    public function getLayout()
    {
        return $this->values["layout"];
    }

    /**
     * Returns the theme to use for the HTML views.
     *
     * @return string
     *            The HTML views theme.
     */
    public function getTheme()
    {
        return $this->values["theme"];
    }

    /**
     * Returns the cache directory name.
     *
     * @return string
     *            The cache directory name. Null if not set.
     */
    public function getCacheDir()
    {
        return $this->values["cacheDir"];
    }

    /**
     * Returns the list of repository url protocols.
     *
     * @return string[]
     *            The list of repository url protocols.
     */
    public function getRepoProtocols()
    {
        return array_keys($this->values["repoBaseUrls"]);
    }

    /**
     * Returns the repository base URL for the specified protocol.
     *
     * @param string $protocol
     *            The protocol.
     * @return string
     *            The repository base URL.
     */
    public function getRepoBaseUrl($protocol)
    {
        return $this->values["repoBaseUrls"][$protocol];
    }

    /**
     * Checks if file size should be displayed in tree.
     *
     * @return boolean
     *            True if file size should be displayed in tree, false if not.
     */
    public function hasFileSizeTreeColumn()
    {
        return $this->values["treeColumns"]["fileSize"];
    }

    /**
     * Check if avatar should be displayed in tree.
     *
     * @return boolean
     *            True if avatar should be displayed in tree, false if not.
     */
    public function hasAuthorAvatarTreeColumn()
    {
        return $this->values["treeColumns"]["authorAvatar"];
    }

    /**
     * Check if last modified timestamp should be displayed in tree.
     *
     * @return boolean
     *            True if last modified timestamp should be displayed in tree,
     *            false if not.
     */
    public function hasLastModifiedTreeColumn()
    {
        return $this->values["treeColumns"]["lastModified"];
    }

    /**
     * Check if author should be displayed in tree.
     *
     * @return boolean
     *            True if author should be displayed in tree, false if not.
     */
    public function hasAuthorTreeColumn()
    {
        return $this->values["treeColumns"]["author"];
    }

    /**
     * Check if commit message should be displayed in tree.
     *
     * @return boolean
     *            True if commit message should be displayed in tree, false if
     *            not.
     */
    public function hasMessageTreeColumn()
    {
        return $this->values["treeColumns"]["message"];
    }

    /**
     * Check if repository description should be displayed in directory
     * listings.
     *
     * @return boolean
     *            True if repository description should be displayed, false if
     *            not.
     */
    public function hasDescriptionTreeColumn()
    {
        return $this->values["treeColumns"]["description"];
    }
}
