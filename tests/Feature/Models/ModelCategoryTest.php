<?php

namespace Tests\Feature\Models;

use Tests\TestCase;
use App\Models\Card;
use App\Models\User;
use App\Models\Category;
use App\Models\Exercise;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ModelCategoryTest extends TestCase
{
    public function test_it_should_create_users_relationship()
    {
        $category = Category::factory()->create(['name'=>'test category']);
        $user_1 = User::factory()->create(['role'=>'0', 'category_id'=>$category->id]);
        $user_2 = User::factory()->create(['role'=>'0', 'category_id'=>$category->id]);

        $stored_category = Category::find($category->id);

        $this->assertEquals($category->users[0]->id, $user_1->id);
        $this->assertEquals($category->users[1]->id, $user_2->id);
        $this->assertEquals($stored_category->users[0]->id, $user_1->id);
        $this->assertEquals($stored_category->users[1]->id, $user_2->id);

        $user_1->delete();
        $user_2->delete();
        $category->delete();
    }

    public function test_it_should_create_exercises_relationship()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['name'=>'test category']);
        $exercise_1 = Exercise::factory()->create(['name'=>'test exercise 1', 'category_id'=>$category->id, 'creator_user_id' => $user->id]);
        $exercise_2 = Exercise::factory()->create(['name'=>'test exercise 2', 'category_id'=>$category->id, 'creator_user_id' => $user->id]);

        $stored_category = Category::find($category->id);

        $this->assertEquals($category->exercises[0]->id, $exercise_1->id);
        $this->assertEquals($category->exercises[1]->id, $exercise_2->id);
        $this->assertEquals($stored_category->exercises[0]->id, $exercise_1->id);
        $this->assertEquals($stored_category->exercises[1]->id, $exercise_2->id);

        $user->delete();
        $exercise_1->delete();
        $exercise_2->delete();
        $category->delete();
    }

    public function test_it_should_create_cards_relationship()
    {
        $user = User::factory()->create(['role'=>'0']);
        $category = Category::factory()->create(['name'=>'test category']);
        $card_1 = Card::factory()->create(['name'=>'test card 1', 'user_id'=>$user->id, 'creator_user_id' => $user->id, 'category_id'=>$category->id]);
        $card_2 = Card::factory()->create(['name'=>'test card 2', 'user_id'=>$user->id, 'creator_user_id' => $user->id, 'category_id'=>$category->id]);

        $stored_category = Category::find($category->id);

        $this->assertEquals($category->cards[0]->id, $card_1->id);
        $this->assertEquals($category->cards[1]->id, $card_2->id);
        $this->assertEquals($stored_category->cards[0]->id, $card_1->id);
        $this->assertEquals($stored_category->cards[1]->id, $card_2->id);

        $card_1->delete();
        $card_2->delete();
        $category->delete();
        $user->delete();
    }
}
