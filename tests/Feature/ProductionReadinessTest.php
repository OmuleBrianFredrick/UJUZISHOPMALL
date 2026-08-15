<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductionReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_phase_twelve_tables_are_migratable(): void
    {
        $this->assertTrue(Schema::hasTable('wishlists'));
        $this->assertTrue(Schema::hasTable('reviews'));
        $this->assertTrue(Schema::hasTable('promotions'));
        $this->assertTrue(Schema::hasTable('loyalty_transactions'));
    }
}
