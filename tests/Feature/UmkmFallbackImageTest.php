<?php

namespace Tests\Feature;

use App\Http\Controllers\UmkmController;
use Tests\TestCase;

class UmkmFallbackImageTest extends TestCase
{
    public function test_it_uses_category_based_fallback_image_when_cover_image_is_empty(): void
    {
        $controller = new UmkmController();

        $this->assertSame(
            'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=900&q=80',
            $controller->resolveCoverImage(null, 'Jasa')
        );

        $this->assertSame(
            'https://images.unsplash.com/photo-1498654896293-37aacf113fd9?auto=format&fit=crop&w=900&q=80',
            $controller->resolveCoverImage('', 'Makanan & Minuman')
        );
    }
}
