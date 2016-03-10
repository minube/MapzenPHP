<?php
namespace Mapzen\Request;

/**
 * Abstract event class
 */
abstract class RequestAbstract
{
    const SERVICE_DOMAIN = '';
    const METHOD_NAME = '';

    const DEFAULT_UNITS = 'kilometers';

    /**
     * @param string $method
     * @param mixed|null $arguments
     * @return bool
     */
    public function __call($method, $arguments = null)
    {
        if (preg_match('/(set|get)(_)?/', $method)) {
            if (substr($method, 0, 3) == "set") {
                $method = lcfirst(preg_replace('/set(_)?/', '', $method));
                if (property_exists($this, $method)) {
                    $this->{$method} = array_pop($arguments);
                    return $this;
                }
                return $this;
            } elseif (substr($method, 0, 3) == "get") {
                $method = lcfirst(preg_replace('/get(_)?/', '', $method));
                if (property_exists($this, $method)) {
                    return $this->{$method};
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            die("method $method does not exist\n");
        }
    }

    /**
     * @return string
     */
    public function getServiceDomain()
    {
        return static::SERVICE_DOMAIN;
    }

    /**
     * @return string
     */
    public function getMethodName()
    {
        return static::METHOD_NAME;
    }

    /**
     * @return string
     */
    abstract public function getFormattedBody();
}
