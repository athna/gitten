<?php
/*
 * Copyright (C) 2013 Klaus Reimer <k@ailis.de>
 * See LICENSE.md for licensing information.
 */

namespace Gitten;

abstract class AbstractCommand implements Command
{
    /** The file for which the command is executed. */
    protected $file;

    /**
     * Returns the file for which the command is executed.
     *
     * @return File
     *            The file.
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Returns the view name for this command. This is only the raw
     * view name without file extension and without layout directory
     * prefix.
     *
     * @return string
     *            The view name.
     */
    private function getViewName()
    {
        $cls = get_class($this);
        $parts = explode("\\", $cls);
        $className = end($parts);
        return lcfirst(substr($className, 0,  -7));
    }

    /**
     * Forwards to the command view.
     */
    protected function view()
    {
        global $cfg;

        require($this->getViewName() . ".php");
    }

    public function execute($file, $args, $params)
    {
        $this->file = $file;
        $this->processArgs($args);
        $this->processParams($params);
        $this->view();
    }

    /**
     * Processes the command arguments.
     *
     * @param string[] $args
     *           The command arguments to process.
     */
    protected function processArgs($args) {}

    /**
     * Processes the command parameters.
     *
     * @param string[string] $params
     *           The command parameters to process.
     */
    protected function processParams($params) {}
}
