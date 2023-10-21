<?php

namespace Tests\Feature\Routes;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;

class RouteCategoriesTest extends TestCase
{
    public function test_not_auth_should_not_get_categories_data()
    {
        $response = $this->json('GET', 'api/v1/categories', ['Accept' => 'application/json']);
        $response->assertStatus(401);
    }

    public function test_not_auth_should_not_create_categories()
    {
        $response = $this->post('api/v1/categories/', [], ['Accept' => 'application/json']);
        $response->assertStatus(401);
    }

    public function test_not_auth_should_not_update_categories()
    {
        $category = Category::factory()->create();
    
        $response = $this->patch('api/v1/categories/'.$category->id, [], ['Accept' => 'application/json',]);
        $stored_data = Category::find($category->id);

        $category->delete();

        $this->assertEquals($stored_data->getAttributes(), $category->getAttributes());
        $response->assertStatus(401);
    }

    public function test_not_auth_should_not_delete_categories()
    {
        $category = Category::factory()->create();
    
        $response = $this->delete('api/v1/categories/'. $category->id, [], ['Accept' => 'application/json']);
        $stored_data = Category::find($category->id);

        if($stored_data) $stored_data->delete();
        
        $this->assertNotNull($stored_data);
        $response->assertStatus(401);
    }
}
