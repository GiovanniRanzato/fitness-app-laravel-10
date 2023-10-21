<?php

namespace Tests\Feature\Models;

use Tests\TestCase;
use App\Models\Card;
use App\Models\User;
use App\Models\Category;


class ModelUserTest extends TestCase
{
    public function test_it_should_create_user_token()
    {
        $user = User::factory()->create(['role'=>'0']);
        $token =  $user->createAuthToken();

        $this->assertNotNull($token);
        $this->assertIsString($token);
        $this->assertNotEmpty($token);
        $this->assertEquals($user->id, $user->tokens()->first()->tokenable_id);
        $this->assertEquals('user-token', $user->tokens()->first()->name);
        $this->assertEquals('user-abilities', $user->tokens()->first()->abilities[0]);
        // $this->assertEquals('read:cards:only_self', $user->tokens()->first()->abilities[0]);
        // $this->assertEquals('read:users:only_self', $user->tokens()->first()->abilities[1]);
        // $this->assertEquals('update:users:only_self', $user->tokens()->first()->abilities[2]);

        $user->tokens()->delete();
        $user->delete();
    }

    public function test_it_should_create_admin_token()
    {
        $admin = User::factory()->create(['role'=>'1']);
        $token =  $admin->createAuthToken();

        $this->assertNotNull($token);
        $this->assertIsString($token);
        $this->assertNotEmpty($token);
        $this->assertEquals($admin->id, $admin->tokens()->first()->tokenable_id);
        $this->assertEquals('admin-token', $admin->tokens()->first()->name);
        $this->assertEquals('admin-abilities', $admin->tokens()->first()->abilities[0]);

        $admin->tokens()->delete();
        $admin->delete();
    }

    public function test_it_should_create_trainer_token()
    {
        $trainer = User::factory()->create(['role'=>'2']);
        $token =  $trainer->createAuthToken();

        $this->assertNotNull($token);
        $this->assertIsString($token);
        $this->assertNotEmpty($token);
        $this->assertEquals($trainer->id, $trainer->tokens()->first()->tokenable_id);
        $this->assertEquals('trainer-token', $trainer->tokens()->first()->name);
        $this->assertEquals('trainer-abilities', $trainer->tokens()->first()->abilities[0]);

        $trainer->tokens()->delete();
        $trainer->delete();
    }

    public function test_it_should_create_category_relationship()
    {
        $user = User::factory()->create(['role'=>'0']);
        $category = Category::factory()->create(['name'=>'test category']);
        $user->category()->associate($category);
        $user->save();
        $stored_user = User::find($user->id);
        

        $this->assertEquals($category->id, $user->category->id);
        $this->assertEquals($category->id, $stored_user->category->id);

        $user->delete();
        $category->delete();

    }

    public function test_it_should_create_cards_relationship()
    {
        $user = User::factory()->create(['role'=>'0']);
        $creator_user = User::factory()->create(['role'=>'2']);
        $card_1 = Card::factory()->create(['name'=>'test card 1', 'user_id'=>$user->id, 'creator_user_id'=>$creator_user->id]);
        $card_2 = Card::factory()->create(['name'=>'test card 2', 'user_id'=>$user->id, 'creator_user_id'=>$creator_user->id]);
        $user->cards()->saveMany([$card_1, $card_2]);

        $stored_user = User::find($user->id);

        $this->assertEquals($card_1->id, $user->cards[0]->id);
        $this->assertEquals($card_2->id, $user->cards[1]->id);
        $this->assertEquals($card_1->id, $stored_user->cards[0]->id);
        $this->assertEquals($card_2->id, $stored_user->cards[1]->id);

        $card_1->delete();
        $card_2->delete();
        $user->delete();
        $creator_user->delete();
    }
}
