<?php

namespace Lazy\View;

use Throwable;

class View
{
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
     * The views compiler.
     *
     * @var \Lazy\View\CompilerInterface
     */
    protected $compiler;

    /**
     * @param string                            $dir
     * @param string|null                       $compiledDir
     * @param mixed[]                           $params
     * @param \Lazy\View\CompilerInterface|null $compiler
     */
    public function __construct(string $dir, ?string $compiledDir = null, array $params = [], ?CompilerInterface $compiler = null)
    {
        $this->dir = rtrim($dir, \DIRECTORY_SEPARATOR);

        if (null === $compiledDir) {
            $this->compiledDir = $this->dir;
        } else {
            $this->compiledDir = rtrim($compiledDir, \DIRECTORY_SEPARATOR);
        }

        $this->params = $params;
        $this->compiler = $compiler ?? new Compiller;
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
     */
    public function render(string $name, array $params = []): string
    {
        return $this->evaluate($path, array_merge($params, $this->params));
    }

    /**
     * Evaluate a path.
     *
     * @param string  $path
     * @param mixed[] $params
     * @return string
     */
    protected function evaluate(string $path, array $params = []): string
    {
        $obLevel = ob_get_level();

        ob_start();

        extract($params, \EXTR_SKIP);

        try {
            include $path;
        } catch (Throwable $e) {
            while (ob_get_level() > $obLevel) {
                ob_end_clean();
            }

            throw $e;
        }

        return ob_get_clean();
    }
}
