<?php

namespace AssertWell\PHPUnitGlobalState;

trait EnvironmentVariables
{
    /**
     * All environment variables being handled by this trait.
     *
     * @var array
     */
    private $_environmentVariables;

    /**
     * @before
     */
    protected function resetEnvironmentVariableRegistry(): void
    {
        $this->_environmentVariables = [];
    }

    /**
     * @after
     */
    protected function resetEnvironmentVariables(): void
    {
        foreach ($this->_environmentVariables as $variable => $value) {
            if (false === $value) {
                putenv($variable);
            } else {
                putenv("${variable}=${value}");
            }
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
    protected function setEnvironmentVariable(string $variable, $value = null): self
    {
        if (! isset($this->_environmentVariables[$variable])) {
            $this->_environmentVariables[$variable] = getenv($variable);
        }

        return null === $value ? putenv($variable) : putenv("${variable}=${value}");
    }

    /**
     * Delete an environment variable.
     *
     * @param string $variable The variable name.
     */
    protected function deleteEnvironmentVariable(string $variable): self
    {
        return $this->setEnvironmentVariable($variable, null);
    }
}
