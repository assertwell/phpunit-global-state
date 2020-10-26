<?php

/**
 * A utility class to ensure we're calling the runkit7_* functions when available, as the runkit_*
 * versions are deprecated in newer versions of PHP.
 */

namespace AssertWell\PHPUnitGlobalState\Support;

/**
 * phpcs:disable Generic.Files.LineLength.TooLong
 * @method bool constant_add(string $constname, mixed $value[, int $newVisibility])
 * @method bool constant_redefine(string $constname, mixed $value[, int $newVisibility])
 * @method bool constant_remove(string $constname)
 * @method bool function_add(string $funcname, string $arglist, string $code[, bool $return_by_reference = NULL[, string $doc_comment = NULL[, string $return_type[, bool $is_strict]]]])
 * @method bool function_copy(string $funcname, string $targetname)
 * @method bool function_redefine(string $funcname, string $arglist, string $code[, bool $return_by_reference = NULL[, string $doc_comment = NULL[, string $return_type[, bool $is_strict]]]])
 * @method bool function_remove(string $funcname)
 * @method bool function_rename(string $funcname, string $newname)
 * @method bool import(string $filename[, int $flags])
 * @method bool  method_add(string $classname, string $methodname, string $args, string $code[, int $flags = RUNKIT7_ACC_PUBLIC[, string $doc_comment = NULL[, string $return_type[, bool $is_strict]]]])
 * @method bool  method_copy(string $dClass, string $dMethod, string $sClass[, string $sMethod])
 * @method bool  method_redefine(string $classname, string $methodname, string $args, string $code[, int $flags = RUNKIT7_ACC_PUBLIC[, string $doc_comment = NULL[, string $return_type[, bool $is_strict]]]])
 * @method bool  method_remove(string $classname, string $methodname)
 * @method bool  method_rename(string $classname, string $methodname, string $newname)
 * @method int   object_id(object $obj) : int
 * @method array superglobals(void)
 * @method array zval_inspect(string $value)
 * phpcs:enable Generic.Files.LineLength.TooLong
 */
class Runkit
{
    /**
     * Dynamically alias methods to the underlying Runkit functions.
     *
     * @throws \BadFunctionCallException if the underlying function does not exist.
     *
     * @param string $method The method name.
     * @param array  $args   Method arguments.
     *
     * @return mixed The return value of the corresponding runkit(7)_* functions.
     */
    public static function __callStatic($name, array $args = [])
    {
        if (function_exists('runkit7_' . $name)) {
            return call_user_func_array('runkit7_' . $name, $args);
        }

        if (function_exists('runkit_' . $name)) {
            return call_user_func_array('runkit7_' . $name, $args);
        }

        throw new \BadFunctionCallException(sprintf(
            'Runkit7 does not include a runkit7_%1$s() function.',
            $name
        ));
    }
}
