<?php

namespace AssertWell\PHPUnitGlobalState;

use AssertWell\PHPUnitGlobalState\Exceptions\FunctionExistsException;
use AssertWell\PHPUnitGlobalState\Exceptions\RunkitException;
use AssertWell\PHPUnitGlobalState\Support\Runkit;

trait Functions
{
    use Concerns\Runkit;

    /**
     * All functions being handled by this trait.
     *
     * @var array[]
     */
    private $functions = [
        'defined'   => [],
        'redefined' => [],
    ];

    /**
     * @after
     *
     * @return void
     */
    protected function restoreFunctions()
    {
        // Reset anything that was modified.
        array_walk($this->functions['redefined'], function ($original, $name) {
            if (function_exists($name)) {
                Runkit::function_remove($name);
            }

            // Put the original back into place.
            Runkit::function_rename($original, $name);

            unset($this->functions['redefined'][$name]);
        });

        array_map([Runkit::class, 'function_remove'], $this->functions['defined']);
        $this->functions['defined'] = [];
    }

    /**
     * Define a new function.
     *
     * @throws \AssertWell\PHPUnitGlobalState\Exceptions\FunctionExistsException
     * @throws \AssertWell\PHPUnitGlobalState\Exceptions\RunkitException
     *
     * @param string   $name    The function name.
     * @param \Closure $closure The function body.
     *
     * @return self
     */
    protected function defineFunction($name, \Closure $closure)
    {
        if (function_exists($name)) {
            throw new FunctionExistsException(sprintf(
                'Function %1$s() already exists. You may redefine it using %2$s::redefineFunction() instead.',
                $name,
                get_class($this)
            ));
        }

        $this->requiresRunkit('defineFunction() requires Runkit be available, skipping.');

        if (! Runkit::function_add($name, $closure)) {
            throw new RunkitException(sprintf('Unable to define function %1$s().', $name));
        }

        $this->functions['defined'][] = $name;

        return $this;
    }

    /**
     * Redefine an existing function.
     *
     * If the function doesn't yet exist, it will be defined.
     *
     * @param string   $name    The function name to be redefined.
     * @param \Closure $closure The new function body.
     *
     * @return self
     */
    protected function redefineFunction($name, \Closure $closure)
    {
        if (! function_exists($name)) {
            return $this->defineFunction($name, $closure);
        }

        $this->requiresRunkit('redefineFunction() requires Runkit be available, skipping.');

        // Back up the original version of the function.
        if (! isset($this->functions['redefined'][$name])) {
            $namespaced = $this->runkitNamespace($name);

            if (! Runkit::function_rename($name, $namespaced)) {
                throw new RunkitException(sprintf('Unable to back up %1$s(), aborting.', $name));
            }

            $this->functions['redefined'][$name] = $namespaced;

            if (! Runkit::function_add($name, $closure)) {
                throw new RunkitException(sprintf('Unable to redefine function %1$s().', $name));
            }
        } else {
            Runkit::function_redefine($name, $closure);
        }

        return $this;
    }

    /**
     * Delete an existing function.
     *
     * @param string $name The function to be deleted.
     *
     * @return self
     */
    protected function deleteFunction($name)
    {
        if (! function_exists($name)) {
            return $this;
        }

        $namespaced = $this->runkitNamespace($name);

        if (! Runkit::function_rename($name, $namespaced)) {
            throw new RunkitException(sprintf('Unable to back up %1$s(), aborting.', $name));
        }

        $this->functions['redefined'][$name] = $namespaced;

        return $this;
    }
}
