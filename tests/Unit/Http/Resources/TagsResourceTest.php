<?php

namespace Tests\Unit\Http\Resources\Tags;

use App\Http\Resources\Tags\TagsResource;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagsResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_tags_resource_returns_expected_array()
    {
        $tag = Tag::factory()->create([
            'name' => 'Promoção',
            'slug' => 'promocao',
        ]);

        $resource = new TagsResource($tag);
        $array = $resource->toArray(request());

        $this->assertIsArray($array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('slug', $array);

        $this->assertEquals('Promoção', $array['name']);
        $this->assertEquals('promocao', $array['slug']);
    }

    public function test_tags_resource_returns_correct_data_without_factory()
    {
        $tag = new Tag([
            'name' => 'Novo',
            'slug' => 'novo',
        ]);

        $resource = new TagsResource($tag);
        $array = $resource->toArray(request());

        $this->assertEquals('Novo', $array['name']);
        $this->assertEquals('novo', $array['slug']);
    }
}
