<?php

namespace Dhii\Factory;

use ArrayAccess;
use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\Factory\Exception\CreateCouldNotMakeExceptionCapableTrait;
use Dhii\Factory\Exception\CreateFactoryExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Invocation\CallbackAwareTrait;
use Dhii\Invocation\Exception\CreateInvocationExceptionCapableTrait;
use Dhii\Invocation\Exception\InvocationExceptionInterface;
use Dhii\Invocation\InvokeCallableCapableTrait;
use Dhii\Util\Normalization\NormalizeArrayCapableTrait;
use Exception as RootException;
use Psr\Container\ContainerInterface;
use stdClass;

/**
 * A concrete implementation of a factory that uses a callback to create subject instances.
 *
 * @since [*next-version*]
 */
abstract class AbstractBaseCallbackFactory implements FactoryInterface
{
    /*
     * Provides functionality for invoking callable things.
     *
     * @since [*next-version*]
     */
    use InvokeCallableCapableTrait;

    /*
     * Provides functionality for normalizing arrays.
     *
     * @since [*next-version*]
     */
    use NormalizeArrayCapableTrait;

    /*
     * Provides functionality for creating invalid-argument exceptions.
     *
     * @since [*next-version*]
     */
    use CreateInvalidArgumentExceptionCapableTrait;

    /*
     * Provides functionality for creating invocation exceptions.
     *
     * @since [*next-version*]
     */
    use CreateInvocationExceptionCapableTrait;

    /*
     * Provides functionality for creating factory exceptions.
     *
     * @since [*next-version*]
     */
    use CreateFactoryExceptionCapableTrait;

    /*
     * Provides functionality for creating could-not-make exceptions.
     *
     * @since [*next-version*]
     */
    use CreateCouldNotMakeExceptionCapableTrait;

    /*
     * Provides string translating functionality.
     *
     * @since [*next-version*]
     */
    use StringTranslatingTrait;

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function make($config = null)
    {
        try {
            return $this->_invokeCallable($this->_getFactoryCallback($config), [$config]);
        } catch (InvocationExceptionInterface $invocationException) {
            throw $this->_createCouldNotMakeException(
                $this->__('Could not make subject instance'),
                null,
                $invocationException,
                $this,
                $config
            );
        } catch (RootException $exception) {
            throw $this->_createFactoryException(
                $this->__('An error occurred while trying to make the subject instance'),
                null,
                $exception,
                $this
            );
        }
    }

    /**
     * Retrieves the factory callback.
     *
     * The factory callback is the callable that will be invoked to create a subject instance.
     * This callback will receive the subject config as the first argument.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|stdClass|ContainerInterface|null $config The subject config, if any.
     *
     * @return callable The callable to invoke.
     */
    abstract protected function _getFactoryCallback($config = null);
}
