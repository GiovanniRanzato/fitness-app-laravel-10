<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ControllerCategoryTest extends TestCase
{
    private $category_attributes = [
        'id',
        'name',
        'color',
        'icon',
        'type',
    ];
    private function get_category_data()
    {
        return [
            'name' => 'test',
            'color' => 'test',
            'icon' => 'test',
            'type' => 'test'
        ];
    }

    private function assert_stored_category_equals_data($stored_category, $data)
    {
        $this->assertEquals($stored_category->name, $data['name']);
        $this->assertEquals($stored_category->color, $data['color']);
        $this->assertEquals($stored_category->icon, $data['icon']);
        $this->assertEquals($stored_category->type, $data['type']);
    }

    private function assert_stored_category_equals_category($stored_category, $category)
    {
        $this->assertEquals($stored_category->name, $category->name,);
        $this->assertEquals($stored_category->color, $category->color);
        $this->assertEquals($stored_category->icon, $category->icon);
        $this->assertEquals($stored_category->type, $category->type);
    }


    // ADMIN TESTS
    public function test_admin_should_get_categories_data()
    {
        $admin = User::factory()->create(['role' => '1']);
        $token = $admin->createAuthToken();

        $response = $this->get('api/v1/categories', ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $admin->tokens()->delete();
        $admin->delete();

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'attributes' => $this->category_attributes
                ]
            ],
            'links',
            'meta'
        ]);
        
    }

    public function test_admin_should_get_category_data()
    {
        $admin = User::factory()->create(['role'=>'1']);
        $token = $admin->createAuthToken();

        $category = Category::factory()->create();

        $response = $this->get('api/v1/categories/'.$category->id, ['Accept' => 'application/json', 'Authorization' => 'Bearer '.$token]);
        $stored_category = Category::find($response->json()['data']['attributes']['id']);
        
        $category->delete();
        $admin->tokens()->delete();
        $admin->delete();

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'attributes' => $this->category_attributes
            ]
        ]);
        $this->assert_stored_category_equals_category($stored_category, $category);

    }

    public function test_admin_should_store_categories()
    {
        $admin = User::factory()->create(['role' => '1']);
        $token = $admin->createAuthToken();

        $create_data = $this->get_category_data();

        $response = $this->post('api/v1/categories/', $create_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $stored_category = Category::find($response->json()['data']['attributes']['id']);

        $stored_category->delete();
        $admin->tokens()->delete();
        $admin->delete();

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'attributes' => $this->category_attributes
            ]
        ]);
        $this->assert_stored_category_equals_data($stored_category, $create_data);
    }

    public function test_admin_should_update_categories()
    {
        $admin = User::factory()->create(['role' => '1']);
        $token = $admin->createAuthToken();

        $category_to_update = Category::factory()->create();

        $update_data = $this->get_category_data();

        $response = $this->patch('api/v1/categories/' . $category_to_update->id, $update_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $stored_data = Category::find($category_to_update->id);
        $admin->tokens()->delete();
        $admin->delete();
        $category_to_update->delete();

        $response->assertJsonStructure([
            'data' => [
                'attributes' => [
                    'id',
                    'name',
                    'color',
                    'icon',
                    'type',
                ]
            ]
        ]);
        $response->assertStatus(200);
        $this->assert_stored_category_equals_data($stored_data, $update_data);
    }

    public function test_admin_should_delete_categories()
    {
        $admin = User::factory()->create(['role' => '1']);
        $token = $admin->createAuthToken();

        $category_to_delete = Category::factory()->create();

        $response = $this->delete('api/v1/categories/' . $category_to_delete->id, [], ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $stored_data = User::find($category_to_delete->id);

        $admin->tokens()->delete();
        $admin->delete();
        if ($category_to_delete) $category_to_delete->delete();

        $this->assertNull($stored_data);
        $response->assertJsonStructure(['message']);
        $response->assertJson(['message' => 'deleted']);
        $response->assertStatus(200);
    }

    // TRAINER TEST
    public function test_trainer_should_get_categories_data()
    {
        $triner = User::factory()->create(['role' => '0']);
        $token = $triner->createAuthToken();

        $response = $this->get('api/v1/categories', ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $triner->tokens()->delete();
        $triner->delete();

        $response->assertStatus(200);
    }

    public function test_trainer_should_not_create_categories()
    {
        $triner = User::factory()->create(['role' => '0']);
        $token = $triner->createAuthToken();

        $create_data = $this->get_category_data();

        $response = $this->post('api/v1/categories/', $create_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $triner->tokens()->delete();
        $triner->delete();

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Access denied: you are not allowed.']);
    }

    public function test_trainer_should_not_update_categories()
    {
        $triner = User::factory()->create(['role' => '0']);
        $token = $triner->createAuthToken();
        $category = Category::factory()->create();

        $update_data = $this->get_category_data();

        $response = $this->patch('api/v1/categories/' . $category->id, $update_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $stored_data = Category::find($category->id);
        $triner->tokens()->delete();
        $triner->delete();
        $category->delete();

        $this->assertEquals($stored_data->getAttributes(), $category->getAttributes());
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Access denied: you are not allowed.']);
    }

    public function test_trainer_should_not_delete_categories()
    {
        $triner = User::factory()->create(['role' => '0']);
        $token = $triner->createAuthToken();
        $category = Category::factory()->create();

        $create_data = $this->get_category_data();

        $response = $this->delete('api/v1/categories/' . $category->id, $create_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $stored_data = Category::find($category->id);
        $triner->tokens()->delete();
        $triner->delete();
        if ($stored_data) $stored_data->delete();

        $this->assertNotNull($stored_data);
        $response->assertStatus(401);
    }

    // USER TEST
    public function test_user_should_get_categories_data()
    {
        $user = User::factory()->create(['role' => '0']);
        $token = $user->createAuthToken();

        $response = $this->get('api/v1/categories', ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $user->tokens()->delete();
        $user->delete();

        $response->assertStatus(200);
    }

    public function test_user_should_get_category_data()
    {
        $user = User::factory()->create(['role' => '0']);
        $category = Category::factory()->create();
        $token = $user->createAuthToken();

        $response = $this->get('api/v1/categories/' . $category->id, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $user->tokens()->delete();
        $user->delete();
        $category->delete();

        $response->assertStatus(200);
    }

    public function test_user_should_not_create_categories()
    {
        $user = User::factory()->create(['role' => '0']);
        $token = $user->createAuthToken();

        $create_data = $this->get_category_data();

        $response = $this->post('api/v1/categories/', $create_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $user->tokens()->delete();
        $user->delete();

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Access denied: you are not allowed.']);
    }

    public function test_user_should_not_update_categories()
    {
        $user = User::factory()->create(['role' => '0']);
        $token = $user->createAuthToken();
        $category = Category::factory()->create();

        $update_data = $this->get_category_data();

        $response = $this->patch('api/v1/categories/' . $category->id, $update_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $stored_data = Category::find($category->id);
        $user->tokens()->delete();
        $user->delete();
        $category->delete();

        $this->assertEquals($stored_data->getAttributes(), $category->getAttributes());

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Access denied: you are not allowed.']);
    }

    public function test_user_should_not_delete_categories()
    {
        $user = User::factory()->create(['role' => '0']);
        $token = $user->createAuthToken();
        $category = Category::factory()->create();

        $create_data = $this->get_category_data();

        $response = $this->delete('api/v1/categories/' . $category->id, $create_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $stored_data = Category::find($category->id);
        $user->tokens()->delete();
        $user->delete();
        if ($stored_data) $stored_data->delete();

        $this->assertNotNull($stored_data);
        $response->assertStatus(401);
    }
}
