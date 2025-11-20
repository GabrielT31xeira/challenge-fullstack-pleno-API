<?php

namespace Tests\Unit\Repositories;

use App\Models\Tag;
use App\Repositories\Tag\TagRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected TagRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repo = new TagRepository(new Tag());
    }

    /** @test */
    public function it_finds_multiple_tags_by_ids()
    {
        $tags = Tag::factory()->count(5)->create();

        $ids = $tags->take(2)->pluck('id')->toArray();

        $result = $this->repo->findMany($ids);

        $this->assertCount(2, $result);

        $this->assertEqualsCanonicalizing($ids, $result->pluck('id')->toArray());

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }
}
