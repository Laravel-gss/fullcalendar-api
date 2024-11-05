<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use Faker\Factory as FakerFactory;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private const TEST_LOGIN_ROUTE = '/api/v1/auth/login';
    private const TEST_REGISTER_ROUTE = '/api/v1/auth/register';
    private const CORRECT_PASSWORD = 'Giovanni98*';
    private const INCORRECT_PASSWORD = '12345670';

    public static function dataProviderUserLogin(): array
    {
        $faker = FakerFactory::create();

        return [
            'Login Successful' => [
                'email' => $faker->email,
                'password' => self::CORRECT_PASSWORD,
                'authenticated' => true,
                'status' => Response::HTTP_OK,
            ],
            'Login Failed' => [
                'email' => $faker->email,
                'password' => self::INCORRECT_PASSWORD,
                'authenticated' => false,
                'status' => Response::HTTP_UNAUTHORIZED,
            ],
        ];
    }

    public static function dataProviderUserRegister(): array
    {
        $faker = FakerFactory::create();

        return [
            'Register Successful' => [
                'name' => $faker->firstName,
                'email' => $faker->email,
                'password' => self::CORRECT_PASSWORD,
                'authenticated' => true,
                'status' => Response::HTTP_CREATED,
            ],
            'Register Failed' => [
                'name' => $faker->firstName,
                'email' => $faker->email,
                'password' => self::INCORRECT_PASSWORD,
                'authenticated' => false,
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ],
            'Register Duplicated' => [
                'name' => $faker->firstName,
                'email' => $faker->email,
                'password' => self::CORRECT_PASSWORD,
                'authenticated' => false,
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'duplicate' => true
            ]
        ];
    }

    public static function dataProviderUserRefreshToken(): array
    {
        return [
            "Refresh Token Successfull" => [
                "token" => null,
                "status" => Response::HTTP_OK
            ],
            "Refresh Token Fail" => [
                "token" => "Invalid Token",
                "status" => Response::HTTP_UNAUTHORIZED,
            ],
        ];
    }

    public static function dataProviderUserLogout(): array
    {
        return [
            "Logout Successfull" => [
                "token" => null,
                "status" => Response::HTTP_OK
            ],
            "Logout Fail" => [
                "token" => "Invalid Token",
                "status" => Response::HTTP_UNAUTHORIZED,
            ],
        ];
    }

    #[DataProvider('dataProviderUserLogin')]
    public function test_user_login(string $email, string $password, bool $authenticated, int $status): void
    {
        User::factory()->create([
            'email' => $email,
            'password' => Hash::make(self::CORRECT_PASSWORD),
        ]);

        $response = $this->postJson(self::TEST_LOGIN_ROUTE, [
            'email' => $email,
            'password' => $password,
        ]);

        $response->assertStatus($status);

        if ($authenticated) {

            $user = $response->json('data.user');
            $token = $response->json('data.token');

            $response->assertJsonStructure(['data' => ['token']]);
            $this->assertNotEmpty($token);

            try {
                $decoded_token = JWTAuth::setToken($token)->getPayload();
                $this->assertEquals($user['id'], $decoded_token['sub']);
            } catch (JWTException $e) {
                $this->fail('Token could not be decoded: ' . $e->getMessage());
            }

            $this->assertAuthenticated();
        } else {
            $response->assertJsonPath('data.token', null);
            $this->assertGuest();
        }
    }

    #[DataProvider('dataProviderUserRegister')]
    public function test_user_register(string $name, string $email, string $password, bool $authenticated, int $status, bool $duplicate = false): void
    {
        if($duplicate) {
            User::factory()->create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ]);
        }

        $response = $this->postJson(self::TEST_REGISTER_ROUTE, [
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        $response->assertStatus($status);

        if ($authenticated) {

            $token = $response->json('data.token');
            $user = $response->json('data.user');

            $response->assertJsonStructure(['data' => ['token']]);
            $this->assertNotEmpty($token);

            try {
                $decoded_token = JWTAuth::setToken($token)->getPayload();
                $this->assertEquals($user['id'], $decoded_token['sub']);
            } catch (JWTException $e) {
                $this->fail('Token could not be decoded: ' . $e->getMessage());
            }

            $this->assertDatabaseHas('users', [
                'email' => $email,
                'name' => $name,
            ]);
        } else {
            $this->assertArrayHasKey('errors', $response->json());
            $this->assertIsArray($response->json('errors'));
        }

    }

    #[DataProvider('dataProviderUserRefreshToken')]
    public function test_refresh_token(string $token = null, int $status): void
    {
        $user_token = $token;

        if(empty($token)) {

            $user = User::factory()->create([
                'name' => $this->faker->firstName,
                'email' => $this->faker->email,
                'password' => Hash::make('12345678'),
            ]);

            $user_token = JWTAuth::fromUser($user);
        }

        $response = $this->withHeaders(['Authorization' => "Bearer $user_token"])
                            ->postJson('/api/v1/auth/refresh-token');

        $response->assertStatus($status);

        if(empty($token)) {
            $response->assertJsonStructure(['data' => ['token']]);
            $new_token = $response->json('data.token');
            $this->assertFalse(JWTAuth::setToken($user_token)->check());
            $this->assertNotEquals($token, $new_token);
        }
    }

    #[DataProvider('dataProviderUserLogout')]
    public function test_user_logout(string $token = null, int $status)
    {
        $user_token = $token;

        if(empty($token)) {

            $user = User::factory()->create([
                'name' => $this->faker->firstName,
                'email' => $this->faker->email,
                'password' => Hash::make('12345678'),
            ]);

            $user_token = JWTAuth::fromUser($user);
        }

        $response = $this->withHeaders(['Authorization' => "Bearer $user_token"])
                            ->postJson('/api/v1/auth/logout');

        $response->assertStatus($status);

        if(empty($token)) {
            $this->assertFalse(JWTAuth::setToken($user_token)->check());
        }
    }

}
