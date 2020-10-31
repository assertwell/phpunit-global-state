<?php

/**
 * A utility class to ensure we're calling the runkit7_* functions when available, as the runkit_*
 * versions are deprecated in newer versions of PHP.
 */

namespace AssertWell\PHPUnitGlobalState\Support;

/**
 * phpcs:disable Generic.Files.LineLength.TooLong
 * @method static bool constant_add(string $constname, mixed $value, int $newVisibility = NULL)
 * @method static bool constant_redefine(string $constname, mixed $value, int $newVisibility = NULL)
 * @method static bool constant_remove(string $constname)
 * @method static bool function_add(string $funcname, string $arglist, string $code, bool $return_by_reference = NULL, string $doc_comment = NULL, string $return_type, bool $is_strict = NULL)
 * @method static bool function_add(string $funcname, \Closure $closure, string $doc_comment = NULL, string $return_type = NULL, bool $is_strict = NULL)
 * @method static bool function_copy(string $funcname, string $targetname)
 * @method static bool function_redefine(string $funcname, string $arglist, string $code, bool $return_by_reference = NULL, string $doc_comment = NULL, string $return_type = NULL, bool $is_strict)
 * @method static bool function_redefine(string $funcname, \Closure $closure, string $doc_comment = NULL, string $return_type = NULL, string $is_strict = NULL)
 * @method static bool function_remove(string $funcname)
 * @method static bool function_rename(string $funcname, string $newname)
 * @method static bool import(string $filename, int $flags = NULL)
 * @method static bool  method_add(string $classname, string $methodname, string $args, string $code, int $flags = RUNKIT7_ACC_PUBLIC, string $doc_comment = NULL, string $return_type = NULL, bool $is_strict = NULL)
 * @method static bool  method_copy(string $dClass, string $dMethod, string $sClass, string $sMethod = NULL)
 * @method static bool  method_redefine(string $classname, string $methodname, string $args, string $code, int $flags = RUNKIT7_ACC_PUBLIC, string $doc_comment = NULL, string $return_type, bool $is_strict = NULL)
 * @method static bool  method_remove(string $classname, string $methodname)
 * @method static bool  method_rename(string $classname, string $methodname, string $newname)
 * @method static int   object_id(object $obj)
 * @method static array superglobals()
 * @method static array zval_inspect(string $value)
 * phpcs:enable Generic.Files.LineLength.TooLong
 */
class Runkit
{
    /**
     * Dynamically alias methods to the underlying Runkit functions.
     *
     * @throws \BadFunctionCallException if the underlying function does not exist.
     *
     * @param string  $name The method name.
     * @param mixed[] $args Method arguments.
     *
     * @return mixed The return value of the corresponding runkit(7)_* functions.
     */
    public static function __callStatic($name, array $args = [])
    {
        if (function_exists('runkit7_' . $name)) {
            return call_user_func_array('runkit7_' . $name, $args);
        }

        if (function_exists('runkit_' . $name)) {
            return call_user_func_array('runkit_' . $name, $args);
        }

        throw new \BadFunctionCallException(sprintf(
            'Neither runkit7_%1$s() nor runkit_%1$s() are defined.',
            $name
        ));
    }
}
