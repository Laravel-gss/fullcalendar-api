<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Enums\Api\FullCalendarEventStatus;
use App\Mail\Api\FullCalendarEventDeletedEmail;
use App\Mail\Api\FullCalendarEventUpdatedEmail;
use App\Mail\Api\NewFullCalendarEventEmail;
use App\Models\FullCalendarEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Faker\Factory as FakerFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\DataProvider;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserEventControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private const TEST_USER_EVENTS_ROUTE = '/api/v1/users/events/';
    private const TEST_USER_PASSWORD = '@A1';
    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            "name" => $this->faker->firstName,
            "email" => $this->faker->email,
            "password" => $this->faker->password(8, 12) . self::TEST_USER_PASSWORD
        ]);


        $this->token = JWTAuth::fromUser($this->user);
    }

    public static function dataProviderNewUserEvent(): array
    {
        $faker = FakerFactory::create();

        return [
            "User Event Created Successfully" => [
                [
                    "name" => $faker->title,
                    "description" => $faker->text,
                    "date" => $faker->date('Y-m-d')
                ],
                "http_status" => Response::HTTP_CREATED,
                "validated" => true
            ],
            "Error creating user event" => [
                [
                    "description" => $faker->text,
                    "date" => $faker->date('Y-m-d')
                ],
                "http_status" => Response::HTTP_UNPROCESSABLE_ENTITY
            ]
        ];
    }

    public static function dataProviderGetUserEvent(): array {

        $faker = FakerFactory::create();

        return [
            "Get user event successfull" => [
                [
                    "name" => $faker->title,
                    "description" => $faker->text,
                    "date" => $faker->date('Y-m-d')
                ],
                "http_status" => Response::HTTP_OK,
            ],
            "Error getting user event" => [
                [],
                "http_status" => Response::HTTP_NOT_FOUND,
                "id" => $faker->uuid()
            ],
        ];
    }

    public static function dataProviderDeleteUserEvent(): array {

        $faker = FakerFactory::create();

        return [
            "Delete user event successfull" => [
                [
                    "name" => $faker->title,
                    "description" => $faker->text,
                    "date" => $faker->date('Y-m-d')
                ],
                "http_status" => Response::HTTP_OK,
            ],
            "Error deleting user event" => [
                [],
                "http_status" => Response::HTTP_NOT_FOUND,
                "id" => $faker->uuid()
            ],
        ];
    }

    public static function dataProviderUpdateserEvent(): array
    {
        $faker = FakerFactory::create();

        return [
            "User Event Updated Successfully" => [
                [
                    "name" => $faker->title,
                    "description" => $faker->text,
                    "date" => $faker->date('Y-m-d')
                ],
                "http_status" => Response::HTTP_OK,
                "validated" => true
            ],
            "Error updating user event / Not found" => [
                [],
                "http_status" => Response::HTTP_NOT_FOUND,
                "id" => $faker->uuid()
            ]
        ];
    }

    #[DataProvider('dataProviderNewUserEvent')]
    public function test_create_new_user_event(array $event_data, int $http_status, bool $validated = false): void
    {
        Mail::fake();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)->postJson(self::TEST_USER_EVENTS_ROUTE, $event_data);

        $user_event = $response->json('data.event');

        $response->assertStatus($http_status);

        if($validated) {
            $this->assertDatabaseHas('full_calendar_events', $event_data);

            $this->assertEquals($user_event['status'], FullCalendarEventStatus::PENDING->value);

            Mail::assertSent(NewFullCalendarEventEmail::class);
        } else {
            $this->assertArrayHasKey('errors', $response->json());
            $this->assertIsArray($response->json('errors'));
        }

    }

    #[DataProvider('dataProviderGetUserEvent')]
    public function test_get_user_event_by_id(array $event_data, int $http_status, string $id = null): void
    {
        $event_id = $id;

        if(empty($id)) {
            $event_data['user_id'] = $this->user->id;
            $user_event = FullCalendarEvent::factory()->create($event_data);
            $event_id = $user_event->id;
        }

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)->getJson(self::TEST_USER_EVENTS_ROUTE . $event_id);

        $response->assertStatus($http_status);

    }

    #[DataProvider('dataProviderDeleteUserEvent')]
    public function test_delete_user_event(array $event_data, int $http_status, string $id = null): void
    {
        Mail::fake();

        $event_id = $id;

        if(empty($id)) {
            $event_data['user_id'] = $this->user->id;
            $user_event = FullCalendarEvent::factory()->create($event_data);
            $event_id = $user_event->id;
        }

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)->deleteJson(self::TEST_USER_EVENTS_ROUTE . $event_id);

        $response->assertStatus($http_status);

        if(empty($id)) {
            $this->assertDatabaseHas('full_calendar_events', [
                'id' => $event_id,
                'deleted_at' => now(),
            ]);

            Mail::assertSent(FullCalendarEventDeletedEmail::class);
        }
    }

    #[DataProvider('dataProviderUpdateserEvent')]
    public function test_update_user_event(array $event_data, int $http_status, bool $validated = false, string $id = null): void
    {
        Mail::fake();

        $event_id = $id;

        if(empty($id)) {
            $user_event = FullCalendarEvent::factory()->create([
                "name" => $this->faker->title,
                "description" => $this->faker->text,
                "date" => $this->faker->date('Y-m-d'),
                "user_id" => $this->user->id
            ]);
            $event_id = $user_event->id;
        }

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)->putJson(self::TEST_USER_EVENTS_ROUTE . $event_id, $event_data);
        $response->assertStatus($http_status);

        if($validated) {

            $this->assertDatabaseHas('full_calendar_events', [
                'id' => $event_id,
                'updated_at' => now(),
            ]);

            Mail::assertSent(FullCalendarEventUpdatedEmail::class);
        } else {
            $this->assertArrayHasKey('errors', $response->json());
            $this->assertIsArray($response->json('errors'));
        }

    }
}
