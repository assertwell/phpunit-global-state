<?php

namespace AssertWell\PHPUnitGlobalState;

use AssertWell\PHPUnitGlobalState\Exceptions\MethodExistsException;
use AssertWell\PHPUnitGlobalState\Exceptions\RunkitException;
use AssertWell\PHPUnitGlobalState\Support\Runkit;

trait Methods
{
    /**
     * All methods being handled by this trait.
     *
     * @var array[]
     */
    private $methods = [
        'defined'   => [],
        'redefined' => [],
    ];

    /**
     * @after
     *
     * @return void
     */
    protected function restoreMethods()
    {
        // Reset anything that was modified.
        array_walk($this->methods['redefined'], function ($methods, $class) {
            foreach ($methods as $modified => $original) {
                if (method_exists($class, $modified)) {
                    Runkit::method_remove($class, $modified);
                }

                // Put the original back into place.
                Runkit::method_rename($class, $original, $modified);
            }

            unset($this->methods['redefined'][$class]);
        });

        array_walk($this->methods['defined'], function ($methods, $class) {
            foreach ($methods as $method) {
                Runkit::method_remove($class, $method);
            }
            unset($this->methods['defined'][$class]);
        });

        Runkit::reset();
    }

    /**
     * Define a new method.
     *
     * @throws \AssertWell\PHPUnitGlobalState\Exceptions\MethodExistsException
     * @throws \AssertWell\PHPUnitGlobalState\Exceptions\RunkitException
     *
     * @param string   $class      The class name.
     * @param string   $name       The method name.
     * @param \Closure $closure    The method body.
     * @param string   $visibility Optional. The method visibility, one of "public", "protected",
     *                             or "private". Default is "public".
     * @param bool     $static     Optional. Whether or not the method should be defined as static.
     *                             Default is false.
     *
     * @return self
     */
    protected function defineMethod($class, $name, \Closure $closure, $visibility = 'public', $static = false)
    {
        if (method_exists($class, $name)) {
            throw new MethodExistsException(sprintf(
                'Method %1$s::%2$s() already exists. You may redefine it using %3$s::redefineMethod() instead.',
                $class,
                $name,
                get_class($this)
            ));
        }

        if (! Runkit::isAvailable()) {
            $this->markTestSkipped('defineMethod() requires Runkit be available, skipping.');
        }

        $flags = Runkit::getVisibilityFlags($visibility, $static);

        if (! Runkit::method_add($class, $name, $closure, $flags)) {
            throw new RunkitException(sprintf('Unable to define method %1$s::%2$s().', $class, $name));
        }

        if (! isset($this->methods['defined'][$class])) {
            $this->methods['defined'][$class] = [];
        }
        $this->methods['defined'][$class][] = $name;

        return $this;
    }

    /**
     * Redefine an existing method.
     *
     * If the method doesn't yet exist, it will be defined.
     *
     * @param string        $class      The class name.
     * @param string        $name       The method name.
     * @param \Closure|null $closure    Optional. A closure representing the method body. If null,
     *                                  the method body will not be replaced. Default is null.
     * @param string        $visibility Optional. The method visibility, one of "public",
     *                                  "protected", or "private". Default is the same as the
     *                                  current value.
     * @param bool          $static     Optional. Whether or not the method should be defined as
     *                                  static. Default is the same is as the current value.
     *
     * @return self
     */
    protected function redefineMethod($class, $name, $closure = null, $visibility = null, $static = null)
    {
        if (! method_exists($class, $name)) {
            if (! $closure instanceof \Closure) {
                throw new RunkitException(
                    sprintf('New method %1$s::$2$s() cannot have an empty body.', $class, $name)
                );
            }

            return $this->defineMethod($class, $name, $closure, $visibility, $static);
        }

        if (! Runkit::isAvailable()) {
            $this->markTestSkipped('redefineMethod() requires Runkit be available, skipping.');
        }

        $method = new \ReflectionMethod($class, $name);

        if (null === $visibility) {
            if ($method->isPrivate()) {
                $visibility = 'private';
            } elseif ($method->isProtected()) {
                $visibility = 'protected';
            } else {
                $visibility = 'public';
            }
        }

        if (null === $static) {
            $static = $method->isStatic();
        }

        $flags = Runkit::getVisibilityFlags($visibility, $static);

        // If $closure is null, copy the existing method body.
        if (null === $closure) {
            $closure = $method->isStatic()
                ? $method->getClosure()
                : $method->getClosure($this->getMockBuilder($class)
                    ->disableOriginalConstructor()
                    ->getMock());
        }

        // Back up the original version of the method.
        if (! isset($this->methods['redefined'][$class][$name])) {
            $prefixed = Runkit::makePrefixed($name);

            if (! Runkit::method_rename($class, $name, $prefixed)) {
                throw new RunkitException(
                    sprintf('Unable to back up %1$s::%2$s(), aborting.', $class, $name)
                );
            }

            if (! isset($this->methods['redefined'][$class])) {
                $this->methods['redefined'][$class] = [];
            }
            $this->methods['redefined'][$class][$name] = $prefixed;

            if (! Runkit::method_add($class, $name, $closure, $flags)) {
                throw new RunkitException(
                    sprintf('Unable to redefine function %1$s::%2$s().', $method, $name)
                );
            }
        } else {
            Runkit::method_redefine($class, $name, $closure, $flags);
        }

        return $this;
    }

    /**
     * Delete an existing method.
     *
     * @param string $class The class name.
     * @param string $name  The method to be deleted.
     *
     * @return self
     */
    protected function deleteMethod($class, $name)
    {
        if (! method_exists($class, $name)) {
            return $this;
        }

        $prefixed = Runkit::makePrefixed($name);

        if (! Runkit::method_rename($class, $name, $prefixed)) {
            throw new RunkitException(
                sprintf('Unable to back up %1$s::%2$s(), aborting.', $class, $name)
            );
        }

        if (! isset($this->methods['redefined'][$class])) {
            $this->methods['redefined'][$class] = [];
        }
        $this->methods['redefined'][$class][$name] = $prefixed;

        return $this;
    }
}
