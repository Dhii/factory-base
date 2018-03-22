<?php

namespace Dhii\Factory\Exception\FuncTest;

use Dhii\Factory\Exception\CouldNotMakeException as TestSubject;
use Exception;
use Xpmock\TestCase;
use Exception as RootException;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class CouldNotMakeExceptionTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\Factory\Exception\CouldNotMakeException';

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

    /**
     * Tests whether a valid instance of the test subject can be created.
     *
     * @since [*next-version*]
     */
    public function testCanBeCreated()
    {
        $subject = new TestSubject();

        $this->assertInstanceOf(
            'Exception',
            $subject,
            'Test subject is not an exception.'
        );

        $this->assertInstanceOf(
            'Dhii\Factory\Exception\CouldNotMakeExceptionInterface',
            $subject,
            'Test subject does not implement expected interface.'
        );
    }

    public function testConstructor()
    {
        $m = uniqid('message-');
        $c = rand(0, 100);
        $p = new Exception();
        $f = $this->mock('Dhii\Factory\FactoryInterface')->make()->new();
        $s = [uniqid('arg-')];

        $subject = new TestSubject($m, $c, $p, $f, $s);

        $this->assertEquals($m, $subject->getMessage(), 'Exception message is incorrect.');
        $this->assertEquals($c, $subject->getCode(), 'Exception code is incorrect.');
        $this->assertSame($p, $subject->getPrevious(), 'Inner exception is incorrect.');
        $this->assertSame($f, $subject->getFactory(), 'Exception factory is incorrect.');
        $this->assertSame($s, $subject->getSubjectConfig(), 'Exception subject config is incorrect.');
    }
}
