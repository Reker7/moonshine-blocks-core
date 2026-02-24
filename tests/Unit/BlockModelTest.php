<?php

declare(strict_types=1);

namespace Reker7\MoonShineBlocksCore\Tests\Unit;

use Reker7\MoonShineBlocksCore\Models\Block;
use Reker7\MoonShineBlocksCore\Models\BlockGroup;
use Reker7\MoonShineBlocksCore\Tests\TestCase;

final class BlockModelTest extends TestCase
{
    // ==================== getFieldsCollection ====================

    public function test_getFieldsCollection_returns_empty_collection_when_no_fields(): void
    {
        $block = Block::create([
            'slug'   => 'empty-block',
            'name'   => 'Empty Block',
            'fields' => null,
        ]);

        $this->assertTrue($block->getFieldsCollection()->isEmpty());
    }

    public function test_getFieldsCollection_returns_collection_from_fields(): void
    {
        $fields = [
            ['name' => 'Title', 'key' => 'title', 'type' => 'text', 'required' => false],
            ['name' => 'Body',  'key' => 'body',  'type' => 'textarea', 'required' => false],
        ];

        $block = Block::create([
            'slug'   => 'text-block',
            'name'   => 'Text Block',
            'fields' => $fields,
        ]);

        $collection = $block->getFieldsCollection();

        $this->assertCount(2, $collection);
        $this->assertSame('title', $collection->get(0)->key);
    }

    // ==================== Scopes ====================

    public function test_scopeEffectiveActive_includes_active_block_without_group(): void
    {
        Block::create(['slug' => 'active-solo', 'name' => 'Active Solo', 'is_active' => true]);

        $this->assertSame(1, Block::effectiveActive()->count());
    }

    public function test_scopeEffectiveActive_excludes_inactive_block(): void
    {
        Block::create(['slug' => 'inactive', 'name' => 'Inactive', 'is_active' => false]);

        $this->assertSame(0, Block::effectiveActive()->count());
    }

    public function test_scopeEffectiveActive_includes_block_in_active_group(): void
    {
        $group = BlockGroup::create(['name' => 'Active Group', 'slug' => 'active-group', 'is_active' => true]);
        Block::create(['slug' => 'in-active-group', 'name' => 'In Active Group', 'is_active' => true, 'block_group_id' => $group->id]);

        $this->assertSame(1, Block::effectiveActive()->count());
    }

    public function test_scopeEffectiveActive_excludes_block_in_inactive_group(): void
    {
        $group = BlockGroup::create(['name' => 'Inactive Group', 'slug' => 'inactive-group', 'is_active' => false]);
        Block::create(['slug' => 'in-inactive-group', 'name' => 'In Inactive Group', 'is_active' => true, 'block_group_id' => $group->id]);

        $this->assertSame(0, Block::effectiveActive()->count());
    }

    public function test_scopeInGroupSlug_filters_blocks_by_group_slug(): void
    {
        $group = BlockGroup::create(['name' => 'Marketing', 'slug' => 'marketing', 'is_active' => true]);
        Block::create(['slug' => 'banner', 'name' => 'Banner', 'block_group_id' => $group->id]);
        Block::create(['slug' => 'hero', 'name' => 'Hero']);

        $result = Block::inGroupSlug('marketing')->get();

        $this->assertCount(1, $result);
        $this->assertSame('banner', $result->first()->slug);
    }

    public function test_scopeInGroupSlug_returns_all_when_slug_is_null(): void
    {
        $group = BlockGroup::create(['name' => 'Group', 'slug' => 'group', 'is_active' => true]);
        Block::create(['slug' => 'in-group', 'name' => 'In Group', 'block_group_id' => $group->id]);
        Block::create(['slug' => 'standalone', 'name' => 'Standalone']);

        $result = Block::inGroupSlug(null)->get();

        $this->assertCount(2, $result);
    }

    public function test_scopeWithoutGroup_returns_only_ungrouped_blocks(): void
    {
        $group = BlockGroup::create(['name' => 'Group', 'slug' => 'grp', 'is_active' => true]);
        Block::create(['slug' => 'ungrouped', 'name' => 'Ungrouped']);
        Block::create(['slug' => 'grouped', 'name' => 'Grouped', 'block_group_id' => $group->id]);

        $result = Block::withoutGroup()->get();

        $this->assertCount(1, $result);
        $this->assertSame('ungrouped', $result->first()->slug);
    }

    // ==================== SoftDeletes ====================

    public function test_soft_deleted_block_is_excluded_from_default_query(): void
    {
        $block = Block::create(['slug' => 'to-delete', 'name' => 'To Delete']);
        $block->delete();

        $this->assertSame(0, Block::count());
    }

    public function test_soft_deleted_block_is_visible_with_withTrashed(): void
    {
        $block = Block::create(['slug' => 'trashed', 'name' => 'Trashed']);
        $block->delete();

        $this->assertSame(1, Block::withTrashed()->count());
    }

    public function test_soft_deleted_block_can_be_restored(): void
    {
        $block = Block::create(['slug' => 'restore-me', 'name' => 'Restore Me']);
        $block->delete();

        Block::withTrashed()->where('slug', 'restore-me')->restore();

        $this->assertSame(1, Block::count());
    }
}
