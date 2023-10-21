<?php

namespace Tests\Feature\Models;

use Tests\TestCase;
use App\Models\Card;
use App\Models\User;
use App\Models\Category;
use App\Models\Exercise;
use App\Models\CardDetail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ModelExerciseTest extends TestCase
{
    public function test_it_should_create_category_relationship()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['name'=>'test category']);
        $exercise = Exercise::factory()->create(['name'=>'test exercise', 'category_id'=>$category->id, 'creator_user_id' => $user->id]);

        $stored_exercise = Exercise::find($exercise->id);

        $this->assertEquals($exercise->category->id, $category->id);
        $this->assertEquals($stored_exercise->category->id, $category->id);

        $user->delete();
        $exercise->delete();
        $category->delete();
    }

    public function test_it_should_create_card_details_relationships()
    {
        $user = User::factory()->create(['role'=>'0']);
        $card = Card::factory()->create(['name'=>'test card', 'user_id'=>$user->id, 'creator_user_id'=>$user->id]);
        $exercise = Exercise::factory()->create(['name'=>'test exercise', 'creator_user_id' => $user->id]);
        $card_detail = CardDetail::factory()->create(['card_id'=>$card->id, 'exercise_id'=>$exercise->id]);

        $stored_exercise = Exercise::find($exercise->id);

        $this->assertEquals($exercise->cardDetails[0]->id, $card_detail->id);
        $this->assertEquals($stored_exercise->cardDetails[0]->id, $card_detail->id);

        $card_detail->delete();
        $card->delete();
        $user->delete();
        $exercise->delete();
    }
}
