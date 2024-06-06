<?php

namespace App\Schemas;

/**
 * @OA\Schema (
 *     title="ResponseSchema",
 *     description="Response",
 * )
 */
class ResponseSchema
{
    /**
     * @OA\Property (
     *     title="Response",
     * )
     * @var object
     */
    public object $data;

    /**
     * @OA\Property (
     *     title="code",
     * )
     * @var int
     */
    public int $code;

    /**
     * @OA\Property (
     *     title="message",
     * )
     * @var string
     */
    public string $message;
}
