<?php

namespace AssertWell\PHPUnitGlobalState;

trait GlobalVariables
{
    /**
     * @var array
     */
    private $globalVariables;

    /**
     * @before
     */
    protected function resetGlobalVariables()
    {
        $this->globalVariables = [
            'created' => [],
            'updated' => [],
        ];
    }

    /**
     * @after
     */
    protected function restoreGlobalVariables()
    {
        // Restore existing values.
        foreach ($this->globalVariables['updated'] as $var => $value) {
            $GLOBALS[$var] = $value;
        }

        // Remove anything that was freshly-defined.
        foreach ($this->globalVariables['created'] as $var) {
            unset($GLOBALS[$var]);
        }
    }

    /**
     * Create or overwrite a global variable for the duration of the test.
     *
     * @param string $variable The global variable name.
     * @param mixed  $value    The new, temporary value. Passing NULL will unset the given
     *                         $variable, if it exists.
     */
    protected function setGlobalVariable(string $variable, $value): void
    {
        if (! isset($GLOBALS[$variable])) {
            $this->globalVariables['created'][] = $variable;
        } elseif (! isset($this->backedUpGlobals['updated'][$variable])) {
            $this->globalVariables['updated'][$variable] = $GLOBALS[$variable];
        }

        if (null === $value) {
            unset($GLOBALS[$variable]);
        } else {
            $GLOBALS[$variable] = $value;
        }
    }
}
