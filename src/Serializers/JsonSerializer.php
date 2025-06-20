<?php

namespace Phpolar\Phpolar\Serializers;

/**
 * Converts data into the JSON data-interchange format.
 */
final class JsonSerializer implements SerializerInterface
{
    /**
     * @param int<1,max> $depth
     * @param callable(mixed $data): string | null $failureHandler
     */
    public function __construct(private int $flags = 0, private int $depth = 512, private $failureHandler = null)
    {
    }

    public function serialize(mixed $data): string
    {
        $result = json_encode($data, $this->flags, $this->depth);

        return $result === false ? $this->handleFailure($data) : $result;
    }

    private function handleFailure(mixed $data): string
    {
        return $this->failureHandler !== null ? ($this->failureHandler)($data) : "";
    }
}
