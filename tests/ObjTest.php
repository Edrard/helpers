<?php

use Edrard\Helpers\Obj;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ObjTest extends TestCase
{
    #[DataProvider('objectToArrayProvider')]
    public function test_it_converts_json_serializable_value_to_array(mixed $input, array $expected): void
    {
        $actual = Obj::obj_to_array($input);

        $this->assertSame($expected, $actual);
    }

    public static function objectToArrayProvider(): array
    {
        return [
            'stdClass object' => [
                (object) [
                    'name' => 'Alex',
                    'role' => 'admin',
                ],
                [
                    'name' => 'Alex',
                    'role' => 'admin',
                ],
            ],
            'nested object' => [
                (object) [
                    'user' => (object) [
                        'name' => 'Alex',
                    ],
                ],
                [
                    'user' => [
                        'name' => 'Alex',
                    ],
                ],
            ],
            'array value' => [
                [
                    'name' => 'Alex',
                ],
                [
                    'name' => 'Alex',
                ],
            ],
            'scalar value returns empty array' => [
                'Alex',
                [],
            ],
        ];
    }

    public function test_it_throws_exception_when_value_cannot_be_encoded(): void
    {
        $resource = fopen('php://memory', 'r');

        try {
            $this->expectException(\InvalidArgumentException::class);
            $this->expectExceptionMessage('Type is not supported');

            Obj::obj_to_array($resource);
        } finally {
            if (is_resource($resource)) {
                fclose($resource);
            }
        }
    }
}
