<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Song;
use Illuminate\Support\Str;

class RadioFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_radio_session_and_next_flow(): void
    {
        // Arrange: create a song to be used by radio queue
        $song = Song::create([
            'id' => Str::random(24),
            'title' => 'Test Song',
            'filename' => 'test.mp3',
            'url' => 'https://example.com/test.mp3',
            'cover_url' => null,
            'category_id' => Str::random(24),
            'anuncio' => false,
            'size_mb' => 1.23,
            'duration_seconds' => 120,
            'company_id' => null,
            'file_hash' => Str::random(32),
        ]);

        // Act: create/resume a radio session using an anonymous session token
        $sessionToken = (string) Str::uuid();
        $sessionRes = $this->withHeaders(['X-Session-Token' => $sessionToken])
            ->getJson('/api/radio/session')
            ->assertStatus(200)
            ->json('data.session');

        $this->assertNotEmpty($sessionRes['id']);
        $this->assertEquals($sessionToken, $sessionRes['session_token']);

        // Request next item
        $nextRes = $this->withHeaders(['X-Session-Token' => $sessionToken])
            ->getJson('/api/radio/next?session_id='.$sessionRes['id'])
            ->assertStatus(200)
            ->json('data.item');

        // Assert we got a playable item
        $this->assertIsArray($nextRes);
        $this->assertArrayHasKey('type', $nextRes);
        $this->assertArrayHasKey('song', $nextRes);
        $this->assertEquals('song', $nextRes['type']);
        $this->assertEquals($song->id, $nextRes['song']['id']);

        // Save progress heartbeat
        $saveRes = $this->withHeaders(['X-Session-Token' => $sessionToken])
            ->postJson('/api/radio/save-progress', [
                'session_id' => $sessionRes['id'],
                'track_id' => $song->id,
                'position' => 42.5,
                'play_queue' => [],
            ])->assertStatus(200)
            ->json('data.session');

        $this->assertEquals(42.5, (float) $saveRes['current_track_position']);
    }
}
