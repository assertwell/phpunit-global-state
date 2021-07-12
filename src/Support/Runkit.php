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
 * @method static bool  method_add(string $classname, string $methodname, \Closure $closure, int $flags = RUNKIT7_ACC_PUBLIC, string $doc_comment = NULL, string $return_type = NULL, bool $is_strict = NULL)
 * @method static bool  method_copy(string $dClass, string $dMethod, string $sClass, string $sMethod = NULL)
 * @method static bool  method_redefine(string $classname, string $methodname, string $args, string $code, int $flags = RUNKIT7_ACC_PUBLIC, string $doc_comment = NULL, string $return_type, bool $is_strict = NULL)
 * @method static bool  method_redefine(string $classname, string $methodname, \Closure $closure, int $flags = RUNKIT7_ACC_PUBLIC, string $doc_comment = NULL, string $return_type, bool $is_strict = NULL)
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
     * A namespace used to move things out of the way for the duration of a test.
     *
     * @var string
     */
    private static $namespace;

    /**
     * A prefix used to move things out of the way for the duration of a test.
     *
     * @var string
     */
    private static $prefix;

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

    /**
     * Determine whether or not Runkit is available in the current environment.
     *
     * @return bool
     */
    public static function isAvailable()
    {
        return function_exists('runkit7_constant_redefine')
            || function_exists('runkit_constant_redefine');
    }

    /**
     * Get the current runkit namespace.
     *
     * If the property is currently empty, one will be created.
     *
     * @return string The namespace (with trailing backslash) where we're moving functions,
     *                constants, etc. during tests.
     */
    public static function getNamespace()
    {
        if (empty(self::$namespace)) {
            self::$namespace = uniqid(__NAMESPACE__ . '\\runkit_') . '\\';
        }

        return self::$namespace;
    }

    /**
     * Get the current runkit prefix.
     *
     * If the property is currently empty, one will be created.
     *
     * @return string The prefix we're applying to renamed methods.
     */
    public static function getPrefix()
    {
        if (empty(self::$prefix)) {
            self::$prefix = str_replace('\\', '_', self::getNamespace());
        }

        return self::$prefix;
    }

    /**
     * Get the appropriate visibility/static flag(s) for defining methods.
     *
     * @param string $visibility The method visibility.
     * @param bool   $static     Optional. Whether or not the method should be defined as static.
     *                           Default is false.
     *
     * @return int The corresponding visibility flag, possibly combined with RUNKIT7_ACC_STATIC
     *             depending on $static.
     */
    public static function getVisibilityFlags($visibility, $static = false)
    {
        if ('protected' === $visibility) {
            if (defined('RUNKIT7_ACC_PROTECTED')) {
                return $static ? RUNKIT7_ACC_PROTECTED | RUNKIT7_ACC_STATIC : RUNKIT7_ACC_PROTECTED;
            }

            return $static ? RUNKIT_ACC_PROTECTED | RUNKIT_ACC_STATIC : RUNKIT_ACC_PROTECTED;
        }

        if ('private' === $visibility) {
            if (defined('RUNKIT7_ACC_PRIVATE')) {
                return $static ? RUNKIT7_ACC_PRIVATE | RUNKIT7_ACC_STATIC : RUNKIT7_ACC_PRIVATE;
            }

            return $static ? RUNKIT_ACC_PRIVATE | RUNKIT_ACC_STATIC : RUNKIT_ACC_PRIVATE;
        }

        if (defined('RUNKIT7_ACC_PUBLIC')) {
            return $static ? RUNKIT7_ACC_PUBLIC | RUNKIT7_ACC_STATIC : RUNKIT7_ACC_PUBLIC;
        }

        return $static ? RUNKIT_ACC_PUBLIC | RUNKIT_ACC_STATIC : RUNKIT_ACC_PUBLIC;
    }

    /**
     * Namespace the given reference.
     *
     * @param string $var The item to be moved into the temporary test namespace.
     *
     * @return string The newly-namespaced item.
     */
    public static function makeNamespaced($var)
    {
        // Strip leading backslashes.
        if (0 === mb_strpos($var, '\\')) {
            $var = mb_substr($var, 1);
        }

        return self::getNamespace() . $var;
    }

    /**
     * Prefix the given reference.
     *
     * @param string $var The item to be given the temporary test prefix.
     *
     * @return string The newly-namespaced item.
     */
    public static function makePrefixed($var)
    {
        return self::getPrefix() . str_replace('\\', '_', $var);
    }

    /**
     * Reset static properties.
     *
     * This is helpful to run before tests in case self::$namespace or self::$prefix get polluted.
     *
     * @return void
     */
    public static function reset()
    {
        self::$namespace = '';
        self::$prefix    = '';
    }
}
