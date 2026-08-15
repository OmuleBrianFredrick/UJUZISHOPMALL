<?php

namespace Tests\Unit;

use App\Models\Promotion;
use Tests\TestCase;

class PromotionTest extends TestCase
{
    public function test_percentage_discount_is_capped_at_subtotal(): void
    {
        $promotion = new Promotion([
            'type' => 'percentage', 'value' => 150, 'minimum_order' => 0,
            'usage_limit' => null, 'usage_count' => 0, 'active' => true,
        ]);

        $this->assertTrue($promotion->isValidFor(100000));
        $this->assertSame(100000.0, $promotion->discountFor(100000));
    }

    public function test_fixed_discount_cannot_exceed_subtotal(): void
    {
        $promotion = new Promotion([
            'type' => 'fixed', 'value' => 25000, 'minimum_order' => 0,
            'usage_limit' => null, 'usage_count' => 0, 'active' => true,
        ]);

        $this->assertSame(10000.0, $promotion->discountFor(10000));
    }

    public function test_exhausted_promotion_is_invalid(): void
    {
        $promotion = new Promotion([
            'type' => 'fixed', 'value' => 1000, 'minimum_order' => 0,
            'usage_limit' => 5, 'usage_count' => 5, 'active' => true,
        ]);

        $this->assertFalse($promotion->isValidFor(10000));
        $this->assertSame(0.0, $promotion->discountFor(10000));
    }
}
