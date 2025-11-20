<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TagTest extends TestCase
{
    /** @test */
    public function it_has_fillable_attributes()
    {
        $tag = new Tag();

        $this->assertEquals([
            'name',
            'slug',
        ], $tag->getFillable());
    }

    /** @test */
    public function it_uses_uuids_as_primary_key()
    {
        $tag = new Tag();

        $this->assertFalse($tag->incrementing);
        $this->assertEquals('string', $tag->getKeyType());
    }

    /** @test */
    public function it_belongs_to_many_products()
    {
        $tag = new Tag();

        $relation = $tag->products();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
    }
}
