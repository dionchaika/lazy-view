<?php

namespace Lazy\View;

class View
{
    /**
     * The view extentions.
     */
    const VIEW_EXT = [

        '.view.js',
        '.view.css',
        '.view.php',
        '.view.html'

    ];

    /**
     * The compiled view extention.
     */
    const COMPILED_VIEW_EXT = '.compiled.php';

    /**
     * Enable compiled views cache.
     *
     * @var bool
     */
    public $enableCache = true;

    /**
     * The views root directory.
     *
     * @var string
     */
    protected $dir;

    /**
     * The compiled views root directory.
     *
     * @var string
     */
    protected $compiledDir;

    /**
     * @param string $dir
     * @param string $compiledDir
     */
    public function __construct(string $dir, string $compiledDir = null)
    {
        $this->dir = rtrim($dir, \DIRECTORY_SEPARATOR);

        if (null === $compiledDir) {
            $this->compiledDir = $this->dir;
        } else {
            $this->compiledDir = rtrim($compiledDir, \DIRECTORY_SEPARATOR);
        }
    }

    /**
     * Get the views root directory.
     *
     * @return string
     */
    public function getDir(): string
    {
        return $this->dir;
    }

    /**
     * Get the compiled views root directory.
     *
     * @return string
     */
    public function getCompiledDir(): string
    {
        return $this->compiledDir;
    }

    /**
     * Render a view into the HTML.
     *
     * @param string  $name
     * @param mixed[] $parameters
     * @return string
     */
    public function render(string $name, array $parameters = []): string
    {
        
    }
}
