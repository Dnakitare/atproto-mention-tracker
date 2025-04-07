<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Mention;
use App\Models\User;

class MentionTest extends TestCase
{
    /**
     * Test that the model casts attributes correctly.
     */
    public function test_model_casts_attributes_correctly(): void
    {
        $mention = Mention::factory()->create([
            'sentiment' => 0.9,
            'post_indexed_at' => now(),
        ]);
        
        $this->assertIsFloat($mention->sentiment);
        $this->assertEquals(0.9, $mention->sentiment);
        
        $this->assertInstanceOf(\Carbon\Carbon::class, $mention->post_indexed_at);
    }
    
    /**
     * Test that the model belongs to a user.
     */
    public function test_model_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $mention = Mention::factory()->create([
            'user_id' => $user->id,
        ]);
        
        $this->assertInstanceOf(User::class, $mention->user);
        $this->assertEquals($user->id, $mention->user->id);
    }
} 