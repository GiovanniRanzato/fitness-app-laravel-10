<?php

namespace Tests\Feature\Controllers;

use DateTime;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ControllerUserTest extends TestCase
{
    private $user_attributes = [
        'id',
        'name',
        'email',
        'role',
        'category_id',
        'last_name',
        'phone',
        'birth_day',
        'sex',
        'weight',
        'height',
        'job',
        'country',
        'city',
        'postal_code',
        'address',
        'role',
        'avatar',
    ];

    private function format_date ($date) {
        $date = New DateTime($date);
        return $date->format('Y-m-d');
    }

    private function get_user_data()
    {
        $date = new DateTime();
        $password = Str::random(10);
        return [
            'name' => Str::random(10),
            'password' => $password,
            'password_confirmation' => $password,
            'email' => Str::random(10).'@'.Str::random(5).'.com',
            'last_name' => Str::random(10),
            'phone' => Str::random(10),
            'birth_day' => $date->format('Y-m-d'),
            'sex' => Str::random(10),
            'weight' => Str::random(10),
            'height' => Str::random(10),
            'job' => Str::random(10),
            'country' => Str::random(10),
            'city' => Str::random(10),
            'postal_code' => Str::random(10),
            'address' => Str::random(10),
            'role' => Str::random(10),
            'avatar' => Str::random(10),
        ];
    }

    private function assert_updated_data_equals_stored_user(Array $updated_data, User $stored_user) {
        foreach ($updated_data as $key => $value) {
            $test[$key] = $value;
            if($key == 'password') {
                $this->assertTrue(password_verify($value, $stored_user->password));
                continue;
            }
            if($key == 'password_confirmation') {
                continue;
            }

            if ($key == 'birth_day') {
                $this->assertEquals($value, $this->format_date($stored_user->$key));
                continue;
            }

            $this->assertEquals($value, $stored_user->$key);
        }
    }

    // ADMIN TESTS
    public function test_admin_should_get_users_data()
    {
        $admin = User::factory()->create(['role' => '1']);
        $token = $admin->createAuthToken();

        $response = $this->get('api/v1/users', ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $admin->tokens()->delete();
        $admin->delete();

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                [ 
                    'attributes' => $this->user_attributes,
                    'included'
                ]
            ],
        ]);
    }

    public function test_admin_should_create_other_users()
    {
        $admin = User::factory()->create(['role' => '1']);
        $token = $admin->createAuthToken();

        $create_data = $this->get_user_data();

        $response = $this->post('api/v1/users/', $create_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $created_user = User::find($response->json()['data']['attributes']['id']);
        
        $created_user->tokens()->delete();
        $created_user->delete();
        
        $admin->tokens()->delete();
        $admin->delete();
        
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'attributes' => $this->user_attributes,
                'included'
            ],
        ]);
    }

    public function test_admin_should_delete_other_users()
    {
        $admin = User::factory()->create(['role' => '1']);
        $token = $admin->createAuthToken();

        $user_to_delete = User::factory()->create(['role' => '0']);

        $response = $this->delete('api/v1/users/' . $user_to_delete->id, [], ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $stored_data = User::find($user_to_delete->id);

        $admin->tokens()->delete();
        $admin->delete();
        if ($user_to_delete) $user_to_delete->delete();

        $this->assertNull($stored_data);
        $response->assertJsonStructure(['message']);
        $response->assertJson(['message' => 'deleted']);
        $response->assertStatus(200);
    }

    public function test_admin_should_update_other_users()
    {
        $admin = User::factory()->create(['role' => '1']);
        $token = $admin->createAuthToken();

        $user_to_update = User::factory()->create(['role' => '0']);
        $updated_data = $this->get_user_data();

        $response = $this->patch('api/v1/users/' . $user_to_update->id, $updated_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $stored_data = User::find($user_to_update->id);

        $admin->tokens()->delete();
        $admin->delete();
        $user_to_update->delete();

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'attributes' => $this->user_attributes,
                'included'
            ]
        ]);
     
        $this->assert_updated_data_equals_stored_user($updated_data, $stored_data);
    }
    // TRAINER TESTS
    public function test_trainer_should_not_delete_other_users()
    {
        $trainer = User::factory()->create(['role' => '2']);
        $token = $trainer->createAuthToken();

        $user_to_delete = User::factory()->create(['role' => '0']);

        $response = $this->delete('api/v1/users/' . $user_to_delete->id, [], ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $stored_data = User::find($user_to_delete->id);

        $trainer->tokens()->delete();
        $trainer->delete();
        $user_to_delete->delete();

        $this->assertNotNull($stored_data);
        $this->assertNotEmpty($stored_data);
        $response->assertJsonStructure(['message']);
        $response->assertJson(['message' => 'Access denied: you are not allowed.']);
        $response->assertStatus(401);
    }

    public function test_trainer_should_get_users_data()
    {
        $admin = User::factory()->create(['role' => '2']);
        $token = $admin->createAuthToken();

        $response = $this->get('api/v1/users', ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $admin->tokens()->delete();
        $admin->delete();
        $response->assertJsonStructure([
            'data' => [
                [
                    'attributes' => $this->user_attributes,
                    'included'
                ]
            ]
        ]);

        $response->assertStatus(200);
    }

    public function test_trainer_should_not_create_other_users()
    {
        $trainer = User::factory()->create(['role' => '2']);
        $token = $trainer->createAuthToken();

        $create_data = $this->get_user_data();

        $response = $this->post('api/v1/users/', $create_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $trainer->tokens()->delete();
        $trainer->delete();

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'message'
        ]);
    }

    public function test_trainer_should_not_update_other_users()
    {
        $trainer = User::factory()->create(['role' => '2']);
        $token = $trainer->createAuthToken();

        $user_to_not_update = User::factory()->create(['role' => '0']);
        $updated_data = $this->get_user_data();

        $response = $this->patch('api/v1/users/' . $user_to_not_update->id, $updated_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $trainer->tokens()->delete();
        $trainer->delete();
        $user_to_not_update->delete();

        $response->assertContent('{"message":"Access denied: you are not allowed."}');
        $response->assertStatus(401);
    }

    // USER TESTS
    public function test_user_should_get_unauthenticaded_while_get_users_data()
    {
        $user = User::factory()->create(['role' => '0']);
        $token = $user->createAuthToken();

        $response = $this->get('api/v1/users', ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $user->tokens()->delete();
        $user->delete();

        $response->assertContent('{"message":"Access denied: you are not allowed."}');
        $response->assertStatus(401);
    }

    public function test_user_should_update_self()
    {
        $user = User::factory()->create(['role' => '0', 'name' => 'test_user']);
        $token = $user->createAuthToken();

        $updated_data = $this->get_user_data();

        $response = $this->patch('api/v1/users/' . $user->id, $updated_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $stored_user = User::find($user->id);

        $user->tokens()->delete();
        $user->delete();

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'attributes' => $this->user_attributes,
                'included'
            ]
        ]);

        $this->assert_updated_data_equals_stored_user($updated_data, $stored_user);
    }

    public function test_user_should_not_update_other_users()
    {
        $user = User::factory()->create(['role' => '0']);
        $token = $user->createAuthToken();

        $user_to_not_update = User::factory()->create(['role' => '0']);
        $updated_data = $this->get_user_data();

        $response = $this->patch('api/v1/users/' . $user_to_not_update->id, $updated_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $user->tokens()->delete();
        $user->delete();
        $user_to_not_update->delete();

        $response->assertContent('{"message":"Access denied: you are not allowed."}');
        $response->assertStatus(401);
    }

    public function test_user_should_not_create_other_users()
    {
        $user = User::factory()->create(['role' => '0']);
        $token = $user->createAuthToken();

        $create_data = $this->get_user_data();

        $response = $this->post('api/v1/users/', $create_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $user->tokens()->delete();
        $user->delete();

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'message'
        ]);
    }

    public function test_user_should_not_delete_other_users()
    {
        $user = User::factory()->create(['role' => '0']);
        $token = $user->createAuthToken();

        $user_to_delete = User::factory()->create(['role' => '0']);

        $response = $this->delete('api/v1/users/' . $user_to_delete->id, [], ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $stored_data = User::find($user_to_delete->id);

        $user->tokens()->delete();
        $user->delete();
        $user_to_delete->delete();

        $this->assertNotNull($stored_data);
        $this->assertNotEmpty($stored_data);
        $response->assertJsonStructure(['message']);
        $response->assertJson(['message' => 'Access denied: you are not allowed.']);
        $response->assertStatus(401);
    }
}