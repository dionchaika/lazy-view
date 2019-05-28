<?php

namespace Lazy\View;

use Throwable;
use InvalidArgumentException;

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
     * The array
     * of shared view parameters.
     *
     * @var mixed[]
     */
    protected $params = [];

    /**
     * @param string      $dir
     * @param string|null $compiledDir
     */
    public function __construct(string $dir, ?string $compiledDir = null)
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
     * Get the array
     * of shared view parameters.
     *
     * @return mixed[]
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Is a shared view parameter exists.
     *
     * @param string $name
     * @return bool
     */
    public function hasParam(string $name): bool
    {
        return isset($this->params[$name]);
    }

    /**
     * Get a shared view parameter.
     *
     * @param string $name
     * @return mixed|null
     */
    public function getParam(string $name)
    {
        return $this->hasParam($name) ? $this->params[$name] : null;
    }

    /**
     * Set a shared view parameter.
     *
     * @param string $name
     * @param mixed  $value
     * @return self
     */
    public function setParam(string $name, $value): self
    {
        $this->params[$name] = $value;
        return $this;
    }

    /**
     * Render a view into the HTML.
     *
     * @param string  $name
     * @param mixed[] $params
     * @return string
     * @throws \InvalidArgumentException
     */
    public function render(string $name, array $params = []): string
    {
        $normalizedName = $this->normalizeName($name);

        $compiledPath = $this->getCompiledPath($normalizedName);
        if (null === $compiledPath) {
            $path = $this->getPath($normalizedName);
            if (null === $path) {
                throw new InvalidArgumentException('View not found: '.$name.'!');
            }

            $this->compile($path);
        }

        return $this->evaluate($compiledPath, $params);
    }

    /**
     * Get a view path.
     *
     * @param string $name
     * @return string|null
     */
    protected function getPath(string $name): ?string
    {
        $paths = glob($this->dir.\DIRECTORY_SEPARATOR.$name.'.*');
        return !empty($paths) ? $paths[0] : null;
    }

    /**
     * Get a compiled view path.
     *
     * @param string $name
     * @return string|null
     */
    protected function getCompiledPath(string $name): ?string
    {
        $path = $this->compiledDir.\DIRECTORY_SEPARATOR.$name.self::COMPILED_VIEW_EXT;
        return file_exists($path) ? $path : null;
    }

    /**
     * Normalize a view name.
     *
     * @param string $name
     * @return string
     */
    protected function normalizeName(string $name): string
    {
        return str_replace('.', \DIRECTORY_SEPARATOR, $name);
    }

    /**
     * Evaluate path.
     *
     * @param string  $path
     * @param mixed[] $params
     * @return string
     */
    protected function evaluate(string $path, array $params = []): string
    {
        $obLevel = ob_get_level();

        ob_start();

        extract($this->params);
        extract($params, \EXTR_SKIP);

        try {
            require $path;
        } catch (Throwable $e) {
            while (ob_get_level() > $obLevel) {
                ob_end_clean();
            }

            throw $e;
        }

        return ob_get_clean();
    }
}
