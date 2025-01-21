<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\TranslationService;
use App\Models\Translation;
use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;
use DB;

class TranslationServiceTest extends TestCase
{
    use RefreshDatabase;

    private $service;
    private $language;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TranslationService();
        $this->language = Language::create(['code' => 'en', 'name' => 'English']);
    }

    public function test_create_translation()
    {
        $data = [
            'key' => 'test.key',
            'content' => 'Test content',
            'language_id' => $this->language->id
        ];

        $translation = $this->service->create($data);
        $this->assertEquals('test.key', $translation->key);
    }

    public function test_update_translation()
    {
        $translation = Translation::create([
            'key' => 'test.key',
            'content' => 'Test content',
            'language_id' => $this->language->id
        ]);

        $data = [
            'key' => 'updated.key',
            'content' => 'Updated content',
            'language_id' => $this->language->id
        ];

        $updated = $this->service->update($translation->id, $data);
        $this->assertEquals('updated.key', $updated->key);
    }

    public function test_search_translations()
    {
        Translation::create([
            'key' => 'test.key',
            'content' => 'Test content',
            'language_id' => $this->language->id
        ]);

        $results = $this->service->search(['key' => 'test']);
        $this->assertNotEmpty($results);
    }

    public function test_get_translations_performance()
    {
        $translations = [];
        for ($i = 0; $i < 10000; $i++) {
            $translations[] = [
                'key' => "test.key.{$i}",
                'content' => "Test content {$i}",
                'language_id' => $this->language->id,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        
        DB::table('translations')->insert($translations);
        
        $startTime = microtime(true);
        $translations = $this->service->getTranslations();
        $executionTime = (microtime(true) - $startTime) * 1000;

        $this->assertNotEmpty($translations); // verifying that its not empty
        $this->assertLessThan(500, $executionTime);
    }
}
