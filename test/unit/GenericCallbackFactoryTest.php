<?php

namespace Dhii\Factory\UnitTest;

use Dhii\Factory\GenericCallbackFactory as TestSubject;
use stdClass;
use Xpmock\TestCase;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class GenericCallbackFactoryTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\Factory\GenericCallbackFactory';

    /**
     * Tests whether a valid instance of the test subject can be created.
     *
     * @since [*next-version*]
     */
    public function testCanBeCreated()
    {
        $subject = new TestSubject(
            function() {
            }
        );

        $this->assertInstanceOf(
            static::TEST_SUBJECT_CLASSNAME,
            $subject,
            'A valid instance of the test subject could not be created.'
        );
    }

    /**
     * Test the `make()` method to assert whether the internal callback is invoked.
     *
     * @since [*next-version*]
     */
    public function testMake()
    {
        $result = new stdClass();
        $config = [
            uniqid('param-'),
            uniqid('param-'),
            uniqid('param-'),
        ];
        $callback = function($argConfig) use ($result, $config) {
            $this->assertEquals($config, $argConfig, 'The config passed to the callback is incorrect.');

            return $result;
        };
        $subject = new TestSubject($callback);

        $actual = $subject->make($config);

        $this->assertEquals(
            $actual,
            $result,
            'The result returned by `make()` is not the result returned by the callback.'
        );
    }
}
