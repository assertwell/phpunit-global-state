<?php

namespace AssertWell\PHPUnitGlobalState;

use AssertWell\PHPUnitGlobalState\Exceptions\RedefineException;

trait Constants
{
    use Concerns\Runkit;

    /**
     * All constants being handled by this trait.
     *
     * @var array[]
     */
    private $_constants;

    /**
     * @before
     */
    protected function resetConstants()
    {
        $this->_constants = [
            'created' => [],
            'updated' => [],
        ];
    }

    /**
     * @after
     */
    protected function restoreConstants()
    {
        foreach ($this->_constants['updated'] as $name => $value) {
            if (defined($name)) {
                runkit_constant_redefine($name, $value);
            } else {
                define($name, $value);
            }
        }

        foreach ($this->_constants['created'] as $name) {
            if (defined($name)) {
                runkit_constant_remove($name);
            }
        }
    }

    /**
     * Register a new constant to be cleaned up.
     *
     * @see runkit_constant_define()
     *
     * @throws \AssertWell\PHPUnitGlobalState\Exceptions\RedefineException
     *
     * @param string $name  The constant name.
     * @param mixed  $value The scalar value to store in the constant.
     */
    protected function setConstant(string $name, $value = null): self
    {
        $this->requiresRunkit('setConstant() requires Runkit be available, skipping.');

        if (defined($name)) {
            if (! isset($this->_constants['updated'][$name])) {
                $this->_constants['updated'][$name] = constant($name);
            }

            try {
                runkit_constant_redefine($name, $value);
            } catch (\Exception $e) {
                throw new RedefineException(sprintf(
                    'Unable to redefine constant "%s" with value "%s".',
                    $name,
                    is_scalar($value) ? $value : json_encode($value)
                ));
            }
        } else {
            $this->_constants['created'][] = $name;
            define($name, $value);
        }

        return $this;
    }

    /**
     * Delete a constant.
     *
     * @param string $name The constant name.
     */
    protected function deleteConstant(string $name): self
    {
        if (! defined($name)) {
            return $this;
        }

        $this->requiresRunkit('deleteConstant() requires Runkit be available, skipping.');

        if (! isset($this->_constants[$name])) {
            $this->_constants['updated'][$name] = constant($name);
        }

        runkit_constant_remove($name);

        return $this;
    }
}
