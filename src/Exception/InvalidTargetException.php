<?php declare(strict_types=1);

namespace Becklyn\Menu\Exception;


class InvalidTargetException extends MenuException
{
    /**
     * @inheritDoc
     */
    public function __construct ($target, ?\Throwable $previous = null)
    {
        parent::__construct(\sprintf(
            "Invalid target, must be RouteTarget or string, but %s given.",
            \is_object($target) ? \get_class($target) : \gettype($target)
        ), $previous);
    }

}
