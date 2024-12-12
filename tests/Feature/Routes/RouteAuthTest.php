<?php

namespace Tests\Feature\Routes;

use Tests\TestCase;
use App\Models\User;
use App\Models\Consent;
use App\Models\TermsOfService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RouteAuthTest extends TestCase
{
    use RefreshDatabase;
    
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
        $terms_of_service = TermsOfService::factory()->create();
        $user_data = [
            'name' => 'register_test',
            'email' => 'user@register.test',
            'password' => 'password',
            'accepted_terms_of_service_id' => $terms_of_service->id
        ];
     
        // Clean up
        $this->clean_up_users_by_email($user_data['email']);

        $response = $this->post('api/v1/register', $user_data, ['Accept' => 'application/json']);
        $response_json = $response->json();
        
        // Clean up After
        $user = User::find($response_json['data']['attributes']['id']);
        $consent = Consent::where('user_id', '=', $user->id)->first();
        if($consent) $consent->delete();
        $terms_of_service->delete();
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
                'category'
            ],
            'token'
        ]);
    }

    public function test_it_should_login_a_user()
    {
        $terms_of_service = TermsOfService::factory()->create();
        $user_data = [
            'name' => 'register_test',
            'email' => 'user@register.test',
            'password' => 'password',
            'accepted_terms_of_service_id' => $terms_of_service->id
        ];

        // Clean up
        $this->clean_up_users_by_email($user_data['email']);


        $create_user = $this->post('api/v1/register', $user_data, ['Accept' => 'application/json']);
        $create_user_json = $create_user->json();

        $token = $create_user['token'];
        $login_response = $this->post('api/v1/login', $user_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer '.$token]);

        $create_user_response = $create_user->json();
        
        // Clean up After
        $user = User::find($create_user_response['data']['attributes']['id']);
        $consent = Consent::where('user_id', '=', $user->id)->first();
        if($consent) $consent->delete();
        $terms_of_service->delete();
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
                'category'
            ],
            'token'
        ]);
    }

    public function test_it_should_logout_a_user()
    {
        $terms_of_service = TermsOfService::factory()->create();
        $user_data = [
            'name' => 'register_test',
            'email' => 'user@register.test',
            'password' => 'password',
            'accepted_terms_of_service_id' => $terms_of_service->id
        ];

        // Clean up
        $this->clean_up_users_by_email($user_data['email']);

        $create_user = $this->post('api/v1/register', $user_data, ['Accept' => 'application/json']);
        $create_user_json = $create_user->json();

        $token = $create_user['token'];
        $logout_response = $this->post('api/v1/logout', $user_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer '.$token]);
        $create_user_response = $create_user->json();
        
        // Clean up After
        $user = User::find($create_user_json['data']['attributes']['id']);
        $consent = Consent::where('user_id', '=', $user->id)->first();
        if($consent) $consent->delete();
        $terms_of_service->delete();
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
