<?php

namespace Tests\Feature\Requests;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;


class RequestCategoryTest extends TestCase
{
    public function test_admin_should_not_create_categories_with_invalid_data()
    {
        $admin = User::factory()->create(['role'=>'1']);
        $token = $admin->createAuthToken();

        $invalid_data = [
            'name' => rand(),
            'color' => rand(),
            'icon' => rand(),
            'type' => rand(),
        ];


        $response = $this->post('api/v1/categories/', $invalid_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer '.$token]);
        
        $admin->tokens()->delete();
        $admin->delete();
        
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'name',
                'color',
                'icon',
                'type',
            ]
        ]);
        
        $response->assertStatus(422);
        $this->assertEquals($response->json()['message'], 'The name field must be a string. (and 3 more errors)');
        $this->assertEquals($response->json()['errors']['name'][0], 'The name field must be a string.');
        $this->assertEquals($response->json()['errors']['color'][0], 'The color field must be a string.');
        $this->assertEquals($response->json()['errors']['icon'][0], 'The icon field must be a string.');
        $this->assertEquals($response->json()['errors']['type'][0], 'The type field must be a string.');
    }

    public function test_admin_should_update_categories_with_invalid_data()
    {
        $admin = User::factory()->create(['role'=>'1']);
        $token = $admin->createAuthToken();

        $category_to_update = Category::factory()->create();

        $invalid_data = [
            'name' => rand(),
            'color' => rand(),
            'icon' => rand(),
            'type' => rand(),
        ];
    
        $response = $this->patch('api/v1/categories/'.$category_to_update->id, $invalid_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer '.$token]);
        $admin->tokens()->delete();
        $admin->delete();
        $category_to_update->delete();
        
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'name',
                'color',
                'icon',
                'type',
            ]
        ]);
        $response->assertStatus(422);
        $this->assertEquals($response->json()['message'], 'The name field must be a string. (and 3 more errors)');
        $this->assertEquals($response->json()['errors']['name'][0], 'The name field must be a string.');
        $this->assertEquals($response->json()['errors']['color'][0], 'The color field must be a string.');
        $this->assertEquals($response->json()['errors']['icon'][0], 'The icon field must be a string.');
        $this->assertEquals($response->json()['errors']['type'][0], 'The type field must be a string.');
    }

}
