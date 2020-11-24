<?php

namespace Tests;

use AssertWell\PHPUnitGlobalState\Exceptions\MethodExistsException;
use AssertWell\PHPUnitGlobalState\Support\Runkit;
use Tests\Stubs\TestClass;

/**
 * @covers AssertWell\PHPUnitGlobalState\Methods
 *
 * @group Methods
 */
class MethodsTest extends TestCase
{
    /**
     * @before
     */
    protected function verifyRunkitIsAvailable()
    {
        if (! Runkit::isAvailable()) {
            $this->markTestSkipped('This test depends on runkit being available.');
        }
    }

    /**
     * @test
     * @testdox defineMethod() should be able to define a new method
     */
    public function defineMethod_should_be_able_to_define_a_new_method()
    {
        $this->assertFalse(method_exists(TestClass::class, 'myMethod'));

        $this->defineMethod(TestClass::class, 'myMethod', function ($return) {
            return $return;
        });

        $this->assertSame(123, (new TestClass())->myMethod(123));

        $this->restoreMethods();
        $this->assertFalse(
            method_exists(TestClass::class, 'myMethod'),
            'The new method should have been undefined.'
        );
    }

    /**
     * @test
     * @testdox defineMethod() should be able to set the visibility and static properties
     * @dataProvider provideVisibilityStaticCombinations
     * @depends defineMethod_should_be_able_to_define_a_new_method
     *
     * @param string $visibility The visibility to apply.
     * @param bool   $static     Whether or not to make the method static.
     */
    public function defineMethod_should_be_able_to_set_visibility_and_static($visibility, $static)
    {
        $this->defineMethod(TestClass::class, 'myMethod', function ($return) {
            return $return;
        }, $visibility, $static);

        $method           = new \ReflectionMethod(TestClass::class, 'myMethod');
        $visibilityMethod = sprintf('is%s', ucwords($visibility));

        $this->assertTrue(
            $method->$visibilityMethod(),
            sprintf('Expected method to be %s.', $visibility)
        );
        $this->assertSame(
            $static,
            $method->isStatic(),
            $static ? 'Expected method to be static' : 'Expected method to not be static'
        );
    }

    /**
     * @test
     * @testdox defineMethod() should throw a warning if the method already exists
     */
    public function defineMethod_should_throw_a_warning_if_the_method_already_exists()
    {
        $this->assertTrue(method_exists(TestClass::class, 'publicMethod'));
        $signature = (string) (new \ReflectionMethod(TestClass::class, 'publicMethod'));

        $this->expectException(MethodExistsException::class);
        $this->defineMethod(TestClass::class, 'publicMethod', function ($return) {
            return $return;
        });

        $this->assertSame(
            $signature,
            (string) (new \ReflectionMethod(TestClass::class, 'publicMethod')),
            'The original method should have been left untouched.'
        );
    }

    /**
     * @test
     * @testdox redefineMethod() should be able to redefine an existing method
     */
    public function redefineMethod_should_be_able_to_redefine_existing_methods()
    {
        $this->assertTrue(method_exists(TestClass::class, 'publicMethod'));
        $signature = (string) (new \ReflectionMethod(TestClass::class, 'publicMethod'));

        $this->redefineMethod(TestClass::class, 'publicMethod', function () {
            return 123;
        });

        $this->assertSame(123, (new TestClass())->publicMethod('some string'));

        $this->restoreMethods();
        $this->assertTrue(method_exists(TestClass::class, 'publicMethod'));
        $this->assertSame(
            $signature,
            (string) (new \ReflectionMethod(TestClass::class, 'publicMethod')),
            'The original method definition should have been restored.'
        );
    }

    /**
     * @test
     * @testdox redefineMethod() should be able to change method visibility
     * @depends redefineMethod_should_be_able_to_redefine_existing_methods
     */
    public function redefineMethod_should_be_able_to_change_method_visibility()
    {
        $this->assertTrue(method_exists(TestClass::class, 'publicMethod'));
        $signature = (string) (new \ReflectionMethod(TestClass::class, 'publicMethod'));

        $this->redefineMethod(TestClass::class, 'publicMethod', function () {
            return 123;
        }, 'protected');

        $method = new \ReflectionMethod(TestClass::class, 'publicMethod');
        $method->setAccessible(true);

        $this->assertSame(123, $method->invoke(new TestClass()));
        $this->assertTrue($method->isProtected());

        $this->restoreMethods();
        $this->assertSame(
            $signature,
            (string) (new \ReflectionMethod(TestClass::class, 'publicMethod')),
            'The original method definition should have been restored.'
        );
        $this->assertTrue((new \ReflectionMethod(TestClass::class, 'publicMethod'))->isPublic());
    }

    /**
     * @test
     * @testdox redefineMethod() should be preserve visibility by default
     * @depends redefineMethod_should_be_able_to_redefine_existing_methods
     */
    public function redefineMethod_should_preserve_visibility()
    {
        $this->assertTrue(method_exists(TestClass::class, 'protectedMethod'));
        $signature = (string) (new \ReflectionMethod(TestClass::class, 'protectedMethod'));

        $this->redefineMethod(TestClass::class, 'protectedMethod', function () {
            return 123;
        });

        $method = new \ReflectionMethod(TestClass::class, 'protectedMethod');
        $this->assertTrue($method->isProtected());
    }

    /**
     * @test
     * @testdox redefineMethod() should be able to make a method static
     * @depends redefineMethod_should_be_able_to_redefine_existing_methods
     */
    public function redefineMethod_should_be_able_to_make_a_method_static()
    {
        $this->assertTrue(method_exists(TestClass::class, 'publicMethod'));
        $signature = (string) (new \ReflectionMethod(TestClass::class, 'publicMethod'));

        $this->redefineMethod(TestClass::class, 'publicMethod', function () {
            return 123;
        }, 'public', true);

        $this->assertTrue((new \ReflectionMethod(TestClass::class, 'publicMethod'))->isStatic());
        $this->assertSame(123, TestClass::publicMethod());

        $this->restoreMethods();
        $this->assertSame(
            $signature,
            (string) (new \ReflectionMethod(TestClass::class, 'publicMethod')),
            'The original method definition should have been restored.'
        );
        $this->assertFalse((new \ReflectionMethod(TestClass::class, 'publicMethod'))->isStatic());
    }

    /**
     * @test
     * @testdox redefineMethod() should be preserve static state by default
     * @depends redefineMethod_should_be_able_to_redefine_existing_methods
     */
    public function redefineMethod_should_preserve_static()
    {
        $this->assertTrue(method_exists(TestClass::class, 'protectedStaticMethod'));
        $signature = (string) (new \ReflectionMethod(TestClass::class, 'protectedStaticMethod'));

        $this->redefineMethod(TestClass::class, 'protectedStaticMethod', function () {
            return 123;
        });

        $method = new \ReflectionMethod(TestClass::class, 'protectedStaticMethod');
        $this->assertTrue($method->isStatic());
    }

    /**
     * @test
     * @testdox Passing a null body to redefineMethod() should leave the original signature
     * @depends redefineMethod_should_be_able_to_redefine_existing_methods
     */
    public function redefineMethod_can_accept_a_null_body()
    {
        $this->assertTrue(method_exists(TestClass::class, 'publicMethod'));
        $signature = (string) (new \ReflectionMethod(TestClass::class, 'publicMethod'));

        $this->redefineMethod(TestClass::class, 'publicMethod', null, 'protected', true);

        $method = new \ReflectionMethod(TestClass::class, 'publicMethod');
        $this->assertTrue($method->isProtected(), 'The visibility changes should have been applied.');
        $this->assertTrue($method->isStatic(), 'The static changes should have been applied.');
    }

    /**
     * @test
     * @testdox redefineMethod() should be able to make a method non-static
     * @depends redefineMethod_should_be_able_to_redefine_existing_methods
     */
    public function redefineMethod_should_be_able_to_make_a_method_nonstatic()
    {
        $this->assertTrue(method_exists(TestClass::class, 'publicStaticMethod'));
        $signature = (string) (new \ReflectionMethod(TestClass::class, 'publicStaticMethod'));

        $this->redefineMethod(TestClass::class, 'publicStaticMethod', function () {
            return 123;
        }, 'public', false);

        $this->assertFalse((new \ReflectionMethod(TestClass::class, 'publicStaticMethod'))->isStatic());
        $this->assertSame(123, (new TestClass())->publicStaticMethod());

        $this->restoreMethods();
        $this->assertSame(
            $signature,
            (string) (new \ReflectionMethod(TestClass::class, 'publicStaticMethod')),
            'The original method definition should have been restored.'
        );
        $this->assertTrue((new \ReflectionMethod(TestClass::class, 'publicStaticMethod'))->isStatic());
    }

    /**
     * @test
     * @testdox redefineMethod() should be able to redefine newly-defined methods
     */
    public function redefineMethod_should_be_able_to_redefine_newly_defined_methods()
    {
        $this->defineMethod(TestClass::class, 'myMethod', function () {
            return 'abc';
        });
        $this->redefineMethod(TestClass::class, 'myMethod', function () {
            return 'xyz';
        });

        $this->assertSame('xyz', (new TestClass())->myMethod());

        $this->restoreMethods();
        $this->assertFalse(
            method_exists(TestClass::class, 'myMethod'),
            'The newly-created method should still be removed.'
        );
    }

    /**
     * @test
     * @testdox redefineMethod() should be able to redefine an existing methods multiple times
     * @depends redefineMethod_should_be_able_to_redefine_existing_methods
     */
    public function redefineMethod_should_be_able_to_redefine_existing_methods_multiple_times()
    {
        $this->assertTrue(method_exists(TestClass::class, 'publicMethod'));
        $signature = (string) (new \ReflectionMethod(TestClass::class, 'publicMethod'));

        $this->redefineMethod(TestClass::class, 'publicMethod', function () {
            return 'first';
        });
        $this->redefineMethod(TestClass::class, 'publicMethod', function () {
            return 'second';
        });
        $this->redefineMethod(TestClass::class, 'publicMethod', function () {
            return 'third';
        });

        $this->assertSame(
            'third',
            (new TestClass())->publicMethod(),
            'Expected the latest re-definition to be used.'
        );

        $this->restoreMethods();
        $this->assertSame(
            $signature,
            (string) (new \ReflectionMethod(TestClass::class, 'publicMethod')),
            'The original method definition should have been restored.'
        );
    }

    /**
     * @test
     * @testdox redefineMethod() should define methods if they do not exist
     * @depends redefineMethod_should_be_able_to_redefine_existing_methods
     */
    public function redefineMethod_should_define_methods_if_they_do_not_exist()
    {
        $this->assertFalse(method_exists(TestClass::class, 'myMethod'));


        $this->redefineMethod(TestClass::class, 'myMethod', function () {
            return 'value';
        });

        $this->assertSame('value', (new TestClass())->myMethod());

        $this->restoreMethods();
        $this->assertFalse(
            method_exists(TestClass::class, 'myMethod'),
            'The new method should have been undefined.'
        );
    }

    /**
     * @test
     * @testdox deleteMethod() should be able to delete methods
     */
    public function deleteMethod_should_be_able_to_delete_methods()
    {
        $this->assertTrue(
            method_exists(TestClass::class, 'publicMethod'),
            'Test is predicated on this method existing.'
        );

        $this->deleteMethod(TestClass::class, 'publicMethod');
        $this->assertFalse(
            method_exists(TestClass::class, 'publicMethod'),
            'The method should have been deleted.'
        );

        $this->restoreMethods();
        $this->assertTrue(
            method_exists(TestClass::class, 'publicMethod'),
            'The method should have been restored.'
        );
    }

    /**
     * @test
     * @testdox deleteMethod() should do nothing if the method does not exist
     */
    public function deleteMethod_should_do_nothing_if_the_method_does_not_exist()
    {
        $this->assertFalse(
            method_exists(TestClass::class, 'someFakeMethod'),
            'Test is predicated on this method NOT existing.'
        );

        $this->deleteMethod(TestClass::class, 'someFakeMethod');

        $this->assertFalse(
            method_exists(TestClass::class, 'someFakeMethod'),
            'Deleting a non-existent method should not do anything.'
        );

        $this->restoreMethods();
        $this->assertFalse(
            method_exists(TestClass::class, 'someFakeMethod'),
            'Nothing should be restored as there was nothing to begin with.'
        );
    }

    /**
     * Provide combinations of visibility and static.
     *
     * @return array[]
     */
    public function provideVisibilityStaticCombinations()
    {
        return [
            'Public, non-static'    => ['public', false],
            'Protected, non-static' => ['protected', false],
            'Private, non-static'   => ['private', false],
            'Public, static'        => ['public', true],
            'Protected, static'     => ['protected', true],
            'Private, static'       => ['private', true],
        ];
    }
}
