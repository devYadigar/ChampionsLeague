<?php

namespace Tests\Unit\Requests;

use Tests\TestCase;
use App\Http\Requests\EntityRequest;
use App\Models\League;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use Illuminate\Routing\Route;

class EntityRequestTest extends TestCase
{
    private EntityRequest $entityRequest;

    private string $ulid;

    protected function setUp(): void
    {
        parent::setUp();
        $this->entityRequest = new EntityRequest();
        $this->ulid = (string) Str::ulid();
    }

    public static function successfulRequestProvider(): array
    {
        return [
            [1],
            [null],
            [0] // week is not included
        ];
    }

    public static function invalidUlidProvider(): array
    {
        return [
            ['invalid data'], //wrong string
            [null], 
            [23], // number
            [false] // do not send 
        ];
    }
    
    
    #[DataProvider('successfulRequestProvider')]
    function test_request_passes(?int $week): void
    {
        $data = [
            'session_id' => $this->ulid,
        ];
        if (is_null($week) or $week > 0) {
            $data['week'] = $week;
        }
        $rules = $this->entityRequest->rules();

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->passes());

    }

    #[DataProvider('invalidUlidProvider')]
    function test_invalid_ulid(mixed $ulid): void
    {
        $data = [];
        if ($ulid or is_null($ulid)) {
            $data['session_id'] = $ulid;
        }

        $rules = $this->entityRequest->rules();

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('session_id', $validator->errors()->messages());
    }

    function test_week_required_if_route_is_playweek(): void
    {
        $league = League::factory()->create();
        $data = [
            'session_id' => $league->getAttribute('ulid')
        ];

        $this->mockRoute('match.playWeek');

        $validator = Validator::make($data, $this->entityRequest->rules());
        $this->entityRequest->withValidator($validator);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('week', $validator->errors()->messages());
    
    }

    function test_session_id_should_exist_if_route_not_league_create(): void
    {
        $data = [
            'session_id' => $this->ulid,
            'week' => 1
        ];

        $this->mockRoute('match.playWeek');

        $validator = Validator::make($data, $this->entityRequest->rules());
        $this->entityRequest->withValidator($validator);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('session_id', $validator->errors()->messages());
    
    }

    function test_session_id_can_be_nonexist_if_route_league_create(): void
    {
        $data = [
            'session_id' => $this->ulid,
            'week' => 1
        ];

        $this->mockRoute('league.create');

        $validator = Validator::make($data, $this->entityRequest->rules());
        $this->entityRequest->withValidator($validator);

        $this->assertTrue($validator->passes());
    }

    private function mockRoute($name) {
        $route = new Route(['GET'], '/some-url', []);
        $route->name($name);


        $this->entityRequest->setRouteResolver(function () use ($route){
            return $route;
        });
    }
}