<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\TrackedKeyword;
use App\Models\User;

class TrackedKeywordTest extends TestCase
{
    /**
     * Test that the model casts attributes correctly.
     */
    public function test_model_casts_attributes_correctly(): void
    {
        $trackedKeyword = TrackedKeyword::factory()->create([
            'is_active' => true,
        ]);
        
        $this->assertIsBool($trackedKeyword->is_active);
        $this->assertTrue($trackedKeyword->is_active);
    }
    
    /**
     * Test that the model belongs to a user.
     */
    public function test_model_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $trackedKeyword = TrackedKeyword::factory()->create([
            'user_id' => $user->id,
        ]);
        
        $this->assertInstanceOf(User::class, $trackedKeyword->user);
        $this->assertEquals($user->id, $trackedKeyword->user->id);
    }
} 