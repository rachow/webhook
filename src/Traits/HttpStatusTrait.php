<?php
/**
 *  @author: $rachow
 */

namespace TicketTailor\Webhook\Traits;

trait HttpStatusTrait
{
    public static function statusCode(): int
    {
        return match (self::class) {
            self::OK => 200,
            self::CREATED => 201,
            self::ACCEPTED => 202,
            self::NO_CONTENT => 204,
            self::MOVED_PERMANENTLY => 301,
            self::FOUND => 302,
            self::BAD_REQUEST => 400,
            self::UNAUTHORIZED => 401,
            self::PAYMENT_REQUIRED => 402,
            self::FORBIDDEN => 403,
            self::NO_FOUND => 404,
            self::UNPROCESSABLE_CONTENT => 422,
            self::TOO_MANY_REQUEST => 429,
            self::INTERNAL_SERVER_ERROR => 500,
            self::NOT_IMPLEMENTED => 501,
            default => 200
        };
    }
}