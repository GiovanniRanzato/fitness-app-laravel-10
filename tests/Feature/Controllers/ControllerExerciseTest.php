<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\Exercise;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ControllerExerciseTest extends TestCase
{
    private $exercise_attributes = [
        'id',
        'name',
        'description',
        'media_url',
        'notes',
    ];

    private function getExerciseData()
    {
        return [
            'name' => Str::random(10),
            'description' => Str::random(100),
            'media_url' => Str::random(20),
            'notes' => Str::random(120),

        ];
    }

    private function assert_stored_exercise_equals_exercise_data($stored_exercise, $exercise_data){
        $this->assertEquals($stored_exercise->name, $exercise_data['name']);
        $this->assertEquals($stored_exercise->description, $exercise_data['description']);
        $this->assertEquals($stored_exercise->media_url, $exercise_data['media_url']);
        $this->assertEquals($stored_exercise->notes, $exercise_data['notes']);
    }

    public function test_admin_should_list_exercises()
    {
        $admin = User::factory()->create(['role' => '1']);
        $trainer = User::factory()->create(['role' => '2']);
        $token = $admin->createAuthToken();
        $exercise1 = Exercise::factory()->create(['creator_user_id' => $admin->id]);
        $exercise2 = Exercise::factory()->create(['creator_user_id' => $trainer->id]);

        $response = $this->get('api/v1/exercises/', ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        
        $stored_data = Exercise::all();
        
        $stored_data->each(function ($item) {
            $item->delete();
        });
        $admin->tokens()->delete();
        $admin->delete();
        $trainer->tokens()->delete();
        $trainer->delete();

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'attributes' => $this->exercise_attributes
                ],
            ],
            'links',
            'meta'
        ]);
        $this->assertTrue(count($response->json()['data']) >= 2);
    }

    public function test_admin_should_filter_exercises()
    {
        $admin = User::factory()->create(['role' => '1']);
        $trainer = User::factory()->create(['role' => '2']);
        $token = $admin->createAuthToken();
        $exercise_to_search = Exercise::factory()->create(['creator_user_id' => $admin->id]);
        $exercise2 = Exercise::factory()->create(['creator_user_id' => $trainer->id]);


        $response = $this->get('api/v1/exercises/?name[like]='.$exercise_to_search->name, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        
        $stored_data = Exercise::all();
        
        $stored_data->each(function ($item) {
            $item->delete();
        });
        $admin->tokens()->delete();
        $admin->delete();
        $trainer->tokens()->delete();
        $trainer->delete();

        $response->assertStatus(200);
        $results = $response->json();

        foreach($results['data'] as $result) {
            $this->assertTrue(strpos($result['attributes']['name'], $exercise_to_search->name) !== false);
        }
    }

    public function test_admin_should_create_exercises()
    {
        $admin = User::factory()->create(['role' => '1']);
        $token = $admin->createAuthToken();
        $exercise_data = $this->getExerciseData();

        $response = $this->post('api/v1/exercises/', $exercise_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $stored_data = Exercise::find($response->json()['data']['attributes']['id']);
        
        $stored_data->delete();
        $admin->tokens()->delete();
        $admin->delete();

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'attributes' => $this->exercise_attributes
            ]
        ]);
        $this->assert_stored_exercise_equals_exercise_data($stored_data, $exercise_data);
    }

    public function test_admin_should_update_exercises()
    {
        $admin = User::factory()->create(['role' => '1']);
        $token = $admin->createAuthToken();
        $exercise = Exercise::factory()->create(['creator_user_id' => $admin->id]);
        $exercise_data = $this->getExerciseData();
        $response = $this->patch('api/v1/exercises/'.$exercise->id, $exercise_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $stored_data = Exercise::find($exercise->id);
        
        $stored_data->delete();
        $admin->tokens()->delete();
        $admin->delete();

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'attributes' => $this->exercise_attributes
            ]
        ]);
        $this->assert_stored_exercise_equals_exercise_data($stored_data, $exercise_data);
    }

    public function test_admin_should_delete_exercises()
    {
        $admin = User::factory()->create(['role' => '1']);
        $token = $admin->createAuthToken();
        $exercise = Exercise::factory()->create(['creator_user_id' => $admin->id]);
        $response = $this->delete('api/v1/exercises/'.$exercise->id, [], ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        
        $stored_data = Exercise::find($exercise->id);
        
        if($stored_data)
            $stored_data->delete();

        $admin->tokens()->delete();
        $admin->delete();

        $response->assertStatus(200);
        
    }

    public function test_trainer_should_list_their_exercises()
    {
        $trainer = User::factory()->create(['role' => '2']);
        $other_trainer = User::factory()->create(['role' => '2']);
        $token = $trainer->createAuthToken();
        $owned_exercise = Exercise::factory()->create(['creator_user_id' => $trainer->id]);
        $not_owned_exercise = Exercise::factory()->create(['creator_user_id' => $other_trainer->id]);

        $response = $this->get('api/v1/exercises/', ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        
        $stored_data = Exercise::all();
        
        $stored_data->each(function ($item) {
            $item->delete();
        });

        $trainer->tokens()->delete();
        $trainer->delete();
        $other_trainer->tokens()->delete();
        $other_trainer->delete();

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'attributes' => $this->exercise_attributes
                ],
            ],
            'links',
            'meta'
        ]);
        $this->assertTrue(count($response->json()['data']) == 1);
    }

    public function test_trainer_should_create_exercises()
    {
        $trainer = User::factory()->create(['role' => '2']);
        $token = $trainer->createAuthToken();
        $exercise_data = $this->getExerciseData();

        $response = $this->post('api/v1/exercises/', $exercise_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $stored_data = Exercise::find($response->json()['data']['attributes']['id']);
        
        $stored_data->delete();
        $trainer->tokens()->delete();
        $trainer->delete();

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'attributes' => $this->exercise_attributes
            ]
        ]);
        $this->assert_stored_exercise_equals_exercise_data($stored_data, $exercise_data);
    }

    public function test_trainer_should_update_their_exercises()
    {
        $trainer = User::factory()->create(['role' => '2']);
        $token = $trainer->createAuthToken();
        $exercise = Exercise::factory()->create(['creator_user_id' => $trainer->id]);
        $exercise_data = $this->getExerciseData();
        $response = $this->patch('api/v1/exercises/'.$exercise->id, $exercise_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $stored_data = Exercise::find($exercise->id);
        
        $stored_data->delete();
        $trainer->tokens()->delete();
        $trainer->delete();

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'attributes' => $this->exercise_attributes
            ]
        ]);
        $this->assert_stored_exercise_equals_exercise_data($stored_data, $exercise_data);
    }


    public function test_trainer_should_not_update_other_trainer_exercises()
    {
        $trainer = User::factory()->create(['role' => '2']);
        $other_trainer = User::factory()->create(['role' => '2']);
        $token = $trainer->createAuthToken();
        $exercise = Exercise::factory()->create(['creator_user_id' => $other_trainer->id]);
        $exercise_data = $this->getExerciseData();
        $response = $this->patch('api/v1/exercises/'.$exercise->id, $exercise_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $stored_data = Exercise::find($exercise->id);
        
        $stored_data->delete();
        $trainer->tokens()->delete();
        $trainer->delete();

        $other_trainer->tokens()->delete();
        $other_trainer->delete();

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'message'
        ]);
        $this->assertEquals($response->json()['message'], 'Access denied: you are not allowed.');
    }
    
    public function test_trainer_should_delete_their_exercises()
    {
        $trainer = User::factory()->create(['role' => '2']);
        $token = $trainer->createAuthToken();
        $exercise = Exercise::factory()->create(['creator_user_id' => $trainer->id]);
        $response = $this->delete('api/v1/exercises/'.$exercise->id, [], ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        
        $stored_data = Exercise::find($exercise->id);
        
        if($stored_data)
            $stored_data->delete();

        $trainer->tokens()->delete();
        $trainer->delete();

        $response->assertStatus(200);
        
    }

    public function test_trainer_should_not_delete_other_trainer_exercises()
    {
        $trainer = User::factory()->create(['role' => '2']);
        $other_trainer = User::factory()->create(['role' => '2']);
        $token = $trainer->createAuthToken();
        $exercise = Exercise::factory()->create(['creator_user_id' => $other_trainer->id]);
        $response = $this->delete('api/v1/exercises/'.$exercise->id, [], ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        
        $stored_data = Exercise::find($exercise->id);
        $stored_data->delete();

        $trainer->tokens()->delete();
        $trainer->delete();
        $other_trainer->tokens()->delete();
        $other_trainer->delete();

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'message'
        ]);
        
        $this->assertEquals($response->json()['message'], 'Access denied: you are not allowed.');
        $this->assertEquals($stored_data->id, $exercise->id);
    }

    public function test_user_should_not_update_exercises()
    {
        $user = User::factory()->create(['role' => '0']);

        $token = $user->createAuthToken();
        $exercise = Exercise::factory()->create(['creator_user_id' => $user->id]);
        $exercise_data = $this->getExerciseData();
        $response = $this->patch('api/v1/exercises/'.$exercise->id, $exercise_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $stored_data = Exercise::find($exercise->id);
        
        $stored_data->delete();
        $user->tokens()->delete();
        $user->delete();


        $response->assertStatus(401);
        $response->assertJsonStructure([
            'message'
        ]);
        $this->assertEquals($response->json()['message'], 'Access denied: you are not allowed.');
    }

    public function test_users_should_not_delete_exercises()
    {
        $user = User::factory()->create(['role' => '0']);
        $token = $user->createAuthToken();
        $exercise = Exercise::factory()->create(['creator_user_id' => $user->id]);
        $response = $this->delete('api/v1/exercises/'.$exercise->id, [], ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        
        $stored_data = Exercise::find($exercise->id);
        $stored_data->delete();

        $user->tokens()->delete();
        $user->delete();

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'message'
        ]);
        
        $this->assertEquals($response->json()['message'], 'Access denied: you are not allowed.');
        $this->assertEquals($stored_data->id, $exercise->id);
    }

}