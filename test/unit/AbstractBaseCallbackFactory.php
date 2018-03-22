<?php

namespace Dhii\Factory\FuncTest;

use Dhii\Factory\AbstractBaseCallbackFactory as TestSubject;
use Exception;
use stdClass;
use Xpmock\TestCase;
use Exception as RootException;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class AbstractBaseCallbackFactory extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\Factory\AbstractBaseCallbackFactory';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @param array $methods The methods to mock.
     *
     * @return TestSubject|MockObject The new instance.
     */
    public function createInstance($methods = [])
    {
        $methods = $this->mergeValues(
            $methods,
            [
                '_getFactoryCallback',
            ]
        );

        $mock = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
                     ->setMethods($methods)
                     ->getMockForAbstractClass();

        return $mock;
    }

    /**
     * Merges the values of two arrays.
     *
     * The resulting product will be a numeric array where the values of both inputs are present, without duplicates.
     *
     * @since [*next-version*]
     *
     * @param array $destination The base array.
     * @param array $source      The array with more keys.
     *
     * @return array The array which contains unique values
     */
    public function mergeValues($destination, $source)
    {
        return array_keys(array_merge(array_flip($destination), array_flip($source)));
    }

    /**
     * Creates a mock that both extends a class and implements interfaces.
     *
     * This is particularly useful for cases where the mock is based on an
     * internal class, such as in the case with exceptions. Helps to avoid
     * writing hard-coded stubs.
     *
     * @since [*next-version*]
     *
     * @param string   $className      Name of the class for the mock to extend.
     * @param string[] $interfaceNames Names of the interfaces for the mock to implement.
     *
     * @return MockObject The object that extends and implements the specified class and interfaces.
     */
    public function mockClassAndInterfaces($className, $interfaceNames = [])
    {
        $paddingClassName = uniqid($className);
        $definition = vsprintf(
            'abstract class %1$s extends %2$s implements %3$s {}',
            [
                $paddingClassName,
                $className,
                implode(', ', $interfaceNames),
            ]
        );
        eval($definition);

        return $this->getMockForAbstractClass($paddingClassName);
    }

    /**
     * Creates a new exception.
     *
     * @since [*next-version*]
     *
     * @param string $message The exception message.
     *
     * @return RootException|MockObject The new exception.
     */
    public function createException($message = '')
    {
        $mock = $this->getMockBuilder('Exception')
                     ->setConstructorArgs([$message])
                     ->getMock();

        return $mock;
    }

    public function testMake()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $config = [uniqid('arg-')];
        $result = new stdClass();
        $callback = function($arg) use ($config, $result) {
            $this->assertEquals($config, $arg, 'Subject config given to callback is incorrect');

            return $result;
        };

        $subject->expects($this->once())
                ->method('_getFactoryCallback')
                ->with($config)
                ->willReturn($callback);

        $actual = $subject->make($config);

        $this->assertSame($result, $actual, 'Factory result is incorrect.');
    }

    public function testMakeCallbackInvocationException()
    {
        $subject = $this->createInstance();

        $config = [uniqid('arg-')];
        $callback = function() {
            throw new Exception();
        };

        $subject->expects($this->once())
                ->method('_getFactoryCallback')
                ->with($config)
                ->willReturn($callback);

        $this->setExpectedException('Dhii\Factory\Exception\CouldNotMakeExceptionInterface');

        $subject->make($config);
    }

    public function testMakeCallbackMiscException()
    {
        $subject = $this->createInstance();

        $config = [uniqid('arg-')];
        $callback = new stdClass();
        $subject->expects($this->once())
                ->method('_getFactoryCallback')
                ->with($config)
                ->willReturn($callback);

        $this->setExpectedException('Dhii\Factory\Exception\FactoryExceptionInterface');

        $subject->make($config);
    }
}
