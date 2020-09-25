<?php

namespace AssertWell\PHPUnitGlobalState;

trait GlobalVariables
{
    /**
     * @var array[]
     */
    private $_globalVariables;

    /**
     * @before
     */
    protected function resetGlobalVariables()
    {
        $this->_globalVariables = [
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
        foreach ($this->_globalVariables['updated'] as $var => $value) {
            $GLOBALS[$var] = $value;
        }

        // Remove anything that was freshly-defined.
        foreach ($this->_globalVariables['created'] as $var) {
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
    protected function setGlobalVariable($variable, $value)
    {
        if (! isset($GLOBALS[$variable])) {
            $this->_globalVariables['created'][] = $variable;
        } elseif (! isset($this->_globalVariables['updated'][$variable])) {
            $this->_globalVariables['updated'][$variable] = $GLOBALS[$variable];
        }

        if (null === $value) {
            unset($GLOBALS[$variable]);
        } else {
            $GLOBALS[$variable] = $value;
        }
    }
}
