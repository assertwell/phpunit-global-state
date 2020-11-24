<?php

namespace Tests\Stubs;

class TestClass
{
    /**
     * @param string $string
     *
     * @return string
     */
    public function publicMethod($string = 'default')
    {
        return 'Public: ' . $string;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected function protectedMethod($string = 'default')
    {
        return 'Protected: ' . $string;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    private function privateMethod($string = 'default')
    {
        return 'Private: ' . $string;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public static function publicStaticMethod($string = 'default')
    {
        return 'Public, static: ' . $string;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected static function protectedStaticMethod($string = 'default')
    {
        return 'Protected, static: ' . $string;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    private static function privateStaticMethod($string = 'default')
    {
        return 'Private, static: ' . $string;
    }
}
