<?php

namespace AssertWell\PHPUnitGlobalState;

trait EnvironmentVariables
{
    /**
     * All environment variables being handled by this trait.
     *
     * @var array
     */
    private $environmentVariables;

    /**
     * @before
     */
    protected function resetEnvironmentVariableRegistry(): void
    {
        $this->environmentVariables = [];
    }

    /**
     * @after
     */
    protected function resetEnvironmentVariables(): void
    {
        foreach ($this->getEnvironmentVariables() as $variable => $value) {
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
     * @param mixed  $value    The value to store in the environment variable.
     */
    protected function setEnv(string $variable, $value = null): bool
    {
        if (! isset($this->environmentVariables[$variable])) {
            $this->environmentVariables[$variable] = getenv($variable);
        }

        return false === $value ? putenv($variable) : putenv("${variable}=${value}");
    }

    /**
     * Delete an environment variable.
     *
     * @param string $variable The variable name.
     */
    protected function deleteEnv(string $variable): bool
    {
        return $this->setEnv($variable, false);
    }

    /**
     * Retrieve known environment variables.
     */
    protected function getEnvironmentVariables(): array
    {
        return $this->environmentVariables;
    }
}
