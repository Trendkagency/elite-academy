<?php

namespace Tests\Feature;

use Tests\TestCase;

class NotFound404Test extends TestCase
{
    public function test_undefined_route_renders_custom_animated_404_view(): void
    {
        $response = $this->get('/non-existent-random-page-12345');

        $response->assertStatus(404);
        $response->assertSee('HTTP 404');
        $response->assertSee('4');
        $response->assertSee('0');
        $response->assertSee('PAGE NOT FOUND');
    }

    public function test_json_request_for_undefined_route_returns_404(): void
    {
        $response = $this->getJson('/ajax/non-existent-endpoint-xyz');

        $response->assertStatus(404);
    }
}
