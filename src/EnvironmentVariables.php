<?php

namespace AssertWell\PHPUnitGlobalState;

trait EnvironmentVariables
{
    /**
     * All environment variables being handled by this trait.
     *
     * @var mixed[]
     */
    private $_environmentVariables;

    /**
     * @before
     */
    protected function resetEnvironmentVariableRegistry()
    {
        $this->_environmentVariables = [];
    }

    /**
     * @after
     */
    protected function restoreEnvironmentVariables()
    {
        foreach ($this->_environmentVariables as $variable => $value) {
            putenv(false === $value ? $variable : "${variable}=${value}");
        }
    }

    /**
     * Register a new environment variable to be cleaned up.
     *
     * @see putenv()
     *
     * @param string $variable The environment variable name.
     * @param mixed  $value    The value to store in the environment variable. Passing NULL will
     *                         delete the environment variable.
     */
    protected function setEnvironmentVariable($variable, $value = null)
    {
        if (! isset($this->_environmentVariables[$variable])) {
            $this->_environmentVariables[$variable] = getenv($variable);
        }

        putenv(null === $value ? $variable : "${variable}=${value}");

        return $this;
    }

    /**
     * Delete an environment variable.
     *
     * @param string $variable The variable name.
     */
    protected function deleteEnvironmentVariable($variable)
    {
        return $this->setEnvironmentVariable($variable, null);
    }
}
