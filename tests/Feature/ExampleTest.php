<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_message_page_renders_chat_send_script(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $response = $this
            ->actingAs($sender)
            ->get('/messages/' . $receiver->id);

        $response->assertOk();
        $response->assertSee('async sendMessage()');
    }

    public function test_message_store_accepts_json_text_message(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $response = $this
            ->actingAs($sender)
            ->postJson('/messages/' . $receiver->id, [
                'message' => 'Hello from test',
            ]);

        $response
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message.message', 'Hello from test');
    }
}
