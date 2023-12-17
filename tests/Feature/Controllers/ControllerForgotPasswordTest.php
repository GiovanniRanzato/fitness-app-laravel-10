<?php

namespace Tests\Feature\Controllers;

use DateTime;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SampleMail extends Mailable
{
    public function build()
    {
        return $this->view('emails.test')->subject('Test Email');
    }
}

class ControllerForgotPasswordTest extends TestCase
{
    /**
     * A basic feature test example.
     */


    public function test_send_reset_link_email(): void
    {        
        $user = User::factory()->create(['role' => '0']);
        $response = $this->post('api/v1/password/email', ["email" => $user->email], ['Accept' => 'application/json']);
        $user->tokens()->delete();
        $user->delete();

        $response->assertStatus(200);
        $response->assertJson([
            'message' => "Success",
        ]);
    }

    public function test_it_should_update_password()
    {
        $user = User::factory()->create(['role' => '0']);

        $token = 'fake-token';
        $result = DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);
        $response = $this->post('/api/v1/password/update', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $user->delete();
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        $response->assertStatus(200);
        $response->assertExactJson(['message' => 'Success']);
    }

    public function test_it_should_fail_update_forgotten_password()
    {
        $user = User::factory()->create(['role' => '0']);

        $token = 'fake-token';

        $response = $this->post('/api/v1/password/update', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'differnt-password',
        ]);

        $response->assertStatus(500);
        $response->assertExactJson(['message' => 'Error updating password.The password field confirmation does not match.']);
    }
}
