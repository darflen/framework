<?php

declare(strict_types=1);

namespace Darflen\Framework\Http;

use InvalidArgumentException;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestHandler implements RequestHandlerInterface
{
    private array $stack = [];

    /**
     * @var callable|null
     */
    private $resolver;

    public function __construct(array $stack, ?callable $resolver = null)
    {
        if (empty($stack)) {
            throw new InvalidArgumentException('Must not be empty');
        }
        $this->stack = $stack;
        if (is_null($resolver)) {
            $resolver = function ($entry) {
                return $entry;
            };
        }
        $this->resolver = $resolver;
        reset($this->stack);
    }

    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $entry = current($this->stack);
        if ($entry === false) {
            throw new InvalidArgumentException('No middleware left to execute and no response sent');
        }
        $middleware = call_user_func($this->resolver, $entry);
        next($this->stack);
        if ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        }
        if ($middleware instanceof RequestHandlerInterface) {
            reset($this->stack);
            return $middleware->handle($request);
        }
        if (is_callable($middleware)) {
            return $middleware($request, $this);
        }
        throw new InvalidArgumentException('Must be a callable or PSR-15 compatible middleware');
    }
}
