<?php

declare(strict_types=1);

namespace ADS\TakeHome\Http;

use ADS\TakeHome\Exceptions\ClientException;
use ADS\TakeHome\Framework\HttpRequest;
use ADS\TakeHome\Framework\HttpResponseInterface;
use ADS\TakeHome\Framework\JsonHttpResponse;
use Closure;

final class HttpErrorHandler
{
    /**
     * @param Closure(HttpRequest):HttpResponseInterface $handler
     *
     * @return Closure(HttpRequest):HttpResponseInterface
     */
    public static function wrap(Closure $handler): Closure
    {
        return static function (HttpRequest $request) use ($handler): HttpResponseInterface {
            try {
                return $handler($request);
            } catch (ClientException $e) {
                return new JsonHttpResponse(["error" => $e->getMessage()], $e->status());
            }
        };
    }
}
