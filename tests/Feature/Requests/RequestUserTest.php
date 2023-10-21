<?php

namespace Tests\Feature\Requests;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RequestUserTest extends TestCase
{
    public function test_it_should_fail_update_user_without_required_data()
    {
        $user = User::factory()->create(['role' => '0']);
        $token = $user->createAuthToken();
        $incorrect_user_data = [
            'name' => '',
            'email' => '',
            'password' => ''
        ];
        $response = $this->patch('api/v1/users/'.$user->id, $incorrect_user_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $user->tokens()->delete();
        $user->delete();

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'name',
                'email',
                'password',
            ]
        ]);
        $this->assertEquals($response->json()['message'], 'The name field must be a string. (and 3 more errors)');
        $this->assertEquals($response->json()['errors']['name'][1], 'The name field is required.');
        $this->assertEquals($response->json()['errors']['email'][0], 'The email field is required.');
        $this->assertEquals($response->json()['errors']['password'][0], 'The password field is required.');
    }

    public function test_it_should_fail_update_user_with_invalid_data(){
        $user = User::factory()->create(['role' => '0']);
        $token = $user->createAuthToken();
        $incorrect_user_data = [
                'name'        => rand(),
                'email'       => rand(),
                'password'    => rand(),
                'category_id' => rand(),
                'last_name'   => rand(),
                'phone'       => rand(),
                'birth_day'   => rand(),
                'sex'         => rand(),
                'weight'      => rand(),
                'height'      => rand(),
                'job'         => rand(),
                'country'     => rand(),
                'city'        => rand(),
                'postal_code' => rand(),
                'address'     => rand(),
                'role'        => rand(),
                'avatar'      => rand()
        ];

        $response = $this->patch('api/v1/users/'.$user->id, $incorrect_user_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $user->tokens()->delete();
        $user->delete();

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'name',        
                'email',       
                'password',    
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
                'avatar'    
            ]
        ]);

        $this->assertEquals($response->json()['message'], 'The name field must be a string. (and 17 more errors)');
        $this->assertEquals($response->json()['errors']['name'][0], 'The name field must be a string.');
        $this->assertEquals($response->json()['errors']['email'][0], 'The email field must be a valid email address.');
        $this->assertEquals($response->json()['errors']['password'][0], 'The password field must be a string.');
        $this->assertEquals($response->json()['errors']['last_name'][0], 'The last name field must be a string.');
        $this->assertEquals($response->json()['errors']['phone'][0], 'The phone field must be a string.');
        $this->assertEquals($response->json()['errors']['birth_day'][0], 'The birth day field must be a string.');
        $this->assertEquals($response->json()['errors']['sex'][0], 'The sex field must be a string.');
        $this->assertEquals($response->json()['errors']['weight'][0], 'The weight field must be a string.');
        $this->assertEquals($response->json()['errors']['height'][0], 'The height field must be a string.');
        $this->assertEquals($response->json()['errors']['job'][0], 'The job field must be a string.');
        $this->assertEquals($response->json()['errors']['country'][0], 'The country field must be a string.');
        $this->assertEquals($response->json()['errors']['city'][0], 'The city field must be a string.');
        $this->assertEquals($response->json()['errors']['postal_code'][0], 'The postal code field must be a string.');
        $this->assertEquals($response->json()['errors']['address'][0], 'The address field must be a string.');
        $this->assertEquals($response->json()['errors']['role'][0], 'The role field must be a string.');
        $this->assertEquals($response->json()['errors']['avatar'][0], 'The avatar field must be a string.');
    }

    public function test_it_should_fail_update_password_with_invalid_category_id(){
        $user = User::factory()->create(['role' => '0']);
        $token = $user->createAuthToken();
        $incorrect_user_data = [
            'category_id' => rand()
        ];
        $response = $this->patch('api/v1/users/'.$user->id, $incorrect_user_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $user->tokens()->delete();
        $user->delete();
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'category_id'
            ]
        ]);
        $this->assertEquals($response->json()['message'], 'The selected category id is invalid.');



    }

    public function test_it_should_fail_update_password_witouth_confirmation(){
        $user = User::factory()->create(['role' => '0']);
        $token = $user->createAuthToken();
        $incorrect_user_data = [
            'password' => '12345678',
        ];
        $response = $this->patch('api/v1/users/'.$user->id, $incorrect_user_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $user->tokens()->delete();
        $user->delete();

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'password'
            ]
        ]);
        $this->assertEquals($response->json()['message'], 'The password field confirmation does not match.');
    }

    public function test_it_should_fail_update_password_with_incorrect_confirmation(){
        $user = User::factory()->create(['role' => '0']);
        $token = $user->createAuthToken();
        $incorrect_user_data = [
            'password' => '12345678',
            'password_confirmation' => ''
        ];
        $response = $this->patch('api/v1/users/'.$user->id, $incorrect_user_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $user->tokens()->delete();
        $user->delete();

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'password'
            ]
        ]);
        $this->assertEquals($response->json()['message'], 'The password field confirmation does not match.');
    }

    public function test_it_should_update_password_with_correct_confirmation(){
        $user = User::factory()->create(['role' => '0']);
        $token = $user->createAuthToken();
        $incorrect_user_data = [
            'password' => '12345678',
            'password_confirmation' => '12345678'
        ];
        $response = $this->patch('api/v1/users/'.$user->id, $incorrect_user_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $user->tokens()->delete();
        $user->delete();


        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data'
        ]);
    }
}
