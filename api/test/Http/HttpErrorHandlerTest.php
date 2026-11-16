<?php

declare(strict_types=1);

namespace ADS\TakeHome\Tests\Http;

use ADS\TakeHome\Exceptions\InvalidInputException;
use ADS\TakeHome\Exceptions\NotFoundException;
use ADS\TakeHome\Framework\HttpRequest;
use ADS\TakeHome\Framework\HttpResponseInterface;
use ADS\TakeHome\Framework\JsonHttpResponse;
use ADS\TakeHome\Http\HttpErrorHandler;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class HttpErrorHandlerTest extends TestCase
{
    #[Test]
    public function passesThroughSuccessfulResponse(): void
    {
        $response = new JsonHttpResponse(["ok" => true]);

        $this->assertSame($response, HttpErrorHandler::wrap(static fn (): HttpResponseInterface => $response)(new HttpRequest([])));
    }

    #[Test]
    public function convertsInvalidInputToBadRequest(): void
    {
        $response = HttpErrorHandler::wrap(static fn (): HttpResponseInterface => throw new InvalidInputException("hibás"))(new HttpRequest([]));

        $this->assertSame(400, $response->getStatus());
        $this->assertSame('{"error":"hibás"}', $response->getBody());
    }

    #[Test]
    public function convertsNotFoundToNotFound(): void
    {
        $response = HttpErrorHandler::wrap(static fn (): HttpResponseInterface => throw new NotFoundException("nincs"))(new HttpRequest([]));

        $this->assertSame(404, $response->getStatus());
    }

    #[Test]
    public function leavesOtherExceptionsToRouter(): void
    {
        $this->expectException(RuntimeException::class);

        HttpErrorHandler::wrap(static fn (): HttpResponseInterface => throw new RuntimeException("db"))(new HttpRequest([]));
    }
}
