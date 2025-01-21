<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Translation;
use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TranslationTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;
        Language::create(['code' => 'en', 'name' => 'English']);
    }

    public function test_can_create_translation(){
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
        ])->postJson('/api/translations',[
            'key' => 'welcome.message',
            'content' => 'Welcome',
            'language_id' => 1
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('translations',['key','welcome.message']);
    }

    public function test_can_update_translation(){
        Translation::create([
            'key' => 'welcome.message',
            'content' => 'Welcome',
            'language_id' => 1
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
        ])->putJson('/api/translations',[
            'key' => 'welcome.updated',
            'content' => 'Updated Welcome',
            'language_id' => 1
        ]);

        $response->assertStatus(200);
        $response->assertDatabaseHas('translations', ['key' => 'welcome.updated']);
    }

    public function test_can_search_translation(){
        Translation::create([
            'key' => 'welcome.message',
            'content' => 'Welcome',
            'language_id' => 1
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
        ])->getJson('/api/translations/search?key=welcome');

        $response->assertStatus(200)
                ->assertJsonStructure(['data']);
    }

    public function test_export_performance()
    {
        Translation::factory()->count(10000)->create();

        $startTime = microtime(true);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/translations/export');

        $executionTime = (microtime(true) - $startTime) * 1000; //converting to milliseconds

        $response->assertStatus(200);
        $this->assertLessThan(500, $executionTime);
    }


}