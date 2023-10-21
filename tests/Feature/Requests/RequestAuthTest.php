<?php

namespace Tests\Feature\Requests;

use Tests\TestCase;

class RequestAuthTest extends TestCase
{

    public function test_it_should_fail_register_a_new_user_without_required_data()
    {
        $incorrect_user_data = [
            'name' => '',
            'email' => '',
            'password' => ''
        ];
        $response = $this->post('api/v1/register', $incorrect_user_data, ['Accept' => 'application/json']);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'name',
                'email',
                'password',
            ]
        ]);
        $this->assertEquals($response->json()['message'], 'The name field is required. (and 2 more errors)');
        $this->assertEquals($response->json()['errors']['name'][0], 'The name field is required.');
        $this->assertEquals($response->json()['errors']['email'][0], 'The email field is required.');
        $this->assertEquals($response->json()['errors']['password'][0], 'The password field is required.');
    }

    public function test_it_should_fail_register_a_new_user_with_invalid_email()
    {
        $incorrect_user_data = [
            'name' => 'test',
            'email' => 'not_a_valid_email',
            'password' => 'password'
        ];
        $response = $this->post('api/v1/register', $incorrect_user_data, ['Accept' => 'application/json']);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'email',
            ]
        ]);
        $this->assertEquals($response->json()['message'], 'The email field must be a valid email address.');
        $this->assertEquals($response->json()['errors']['email'][0], 'The email field must be a valid email address.');
    }

    public function test_it_should_falil_login_without_required_data()
    {
        $incorrect_user_data = [
            'name' => '',
            'email' => ''
        ];
        $response = $this->post('api/v1/login', $incorrect_user_data, ['Accept' => 'application/json']);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'email',
                'password',
            ]
        ]);
        $this->assertEquals($response->json()['message'], 'The email field is required. (and 1 more error)');
        $this->assertEquals($response->json()['errors']['email'][0], 'The email field is required.');
        $this->assertEquals($response->json()['errors']['password'][0], 'The password field is required.');

    }

    public function test_it_should_fail_login_with_incorect_email()
    {
        $incorrect_user_data = [
            'email' => 'this_is_not_a_valid_email',
            'password' => 'password'
        ];
        $response = $this->post('api/v1/login', $incorrect_user_data, ['Accept' => 'application/json']);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'email',
            ]
        ]);
        $this->assertEquals($response->json()['message'], 'The email field must be a valid email address.');
        $this->assertEquals($response->json()['errors']['email'][0], 'The email field must be a valid email address.');

    }
}
