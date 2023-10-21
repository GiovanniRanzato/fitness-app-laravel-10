<?php

namespace Tests\Feature\Routes;

use Tests\TestCase;
use App\Models\User;

class RouteAuthTest extends TestCase
{
    private function clean_up_users_by_email($email)
    {
        $users = User::where('email', $email)->get();
        foreach($users as $user) {
            $user->tokens()->delete();
            $user->delete();
        }
        $users = User::where('email', $email);
        if ($users->count() > 0) {
            dd('Error: User not deleted');
        }
    }
    public function test_it_should_register_a_new_user()
    {
        $user_data = [
            'name' => 'register_test',
            'email' => 'user@register.test',
            'password' => 'password'
        ];

        // Clean up
        $this->clean_up_users_by_email($user_data['email']);


        $response = $this->post('api/v1/register', $user_data, ['Accept' => 'application/json']);
        
        // Clean up After
        $response_json = $response->json();
        $user = User::find($response_json['data']['attributes']['id']);
        $user->tokens()->delete();
        $user->delete();

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'attributes' => [
                    'id',
                    'name',
                    'email',
                    'role',
                    'category_id'
                ],
                'included'
            ],
            'token'
        ]);
    }

    public function test_it_should_login_a_user()
    {
        $user_data = [
            'name' => 'login_test',
            'email' => 'user@login.test',
            'password' => 'password'
        ];

        // Clean up
        $this->clean_up_users_by_email($user_data['email']);


        $create_user = $this->post('api/v1/register', $user_data, ['Accept' => 'application/json']);
        $create_user_json = $create_user->json();

        $token = $create_user['token'];
        $login_response = $this->post('api/v1/login', $user_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer '.$token]);

        // Clean up After
        $user = User::find($create_user_json['data']['attributes']['id']);
        $user->tokens()->delete();
        $user->delete();

        $login_response->assertStatus(200);
        $login_response->assertJsonStructure([
            'data' => [
                'attributes' => [
                    'id',
                    'name',
                    'email',
                    'role',
                    'category_id'
                ],
                'included'
            ],
            'token'
        ]);
    }

    public function test_it_should_logout_a_user()
    {
        $user_data = [
            'name' => 'logout_test',
            'email' => 'user@logout.test',
            'password' => 'password'
        ];

        // Clean up
        $this->clean_up_users_by_email($user_data['email']);

        $create_user = $this->post('api/v1/register', $user_data, ['Accept' => 'application/json']);
        $create_user_json = $create_user->json();

        $token = $create_user['token'];
        $logout_response = $this->post('api/v1/logout', $user_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer '.$token]);

        // Clean up After
        $user = User::find($create_user_json['data']['attributes']['id']);
        $user->delete();
    
        $logout_response->assertStatus(200);
        $logout_response->assertJsonStructure([
            'message'
        ]);
        $logout_response->assertJson([
            'message' => 'Logged out'
        ]);
        $this->assertEmpty($user->tokens);
    }
}
