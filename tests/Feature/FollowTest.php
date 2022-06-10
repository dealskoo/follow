<?php

namespace Dealskoo\Follow\Tests\Feature;

use Closure;
use Dealskoo\Follow\Events\Followed;
use Dealskoo\Follow\Events\Unfollowed;
use Dealskoo\Follow\Tests\Post;
use Dealskoo\Follow\Tests\Product;
use Dealskoo\Follow\Tests\TestCase;
use Dealskoo\Follow\Tests\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class FollowTest extends TestCase
{
    use RefreshDatabase;

    public function test_basic_features()
    {
        Event::fake();
        $user = User::create(['name' => 'user']);
        $post = Post::create(['title' => 'test guide']);
        $user->follow($post);
        Event::assertDispatched(Followed::class, function ($event) use ($user, $post) {
            $follow = $event->follow;
            return $follow->followable instanceof Post && $follow->user instanceof User && $follow->user->id == $user->id && $follow->followable->id == $post->id;
        });

        $this->assertTrue($user->hasFollowed($post));
        $this->assertTrue($post->isFollowedBy($user));

        $user->unfollow($post);
        Event::assertDispatched(Unfollowed::class, function ($event) use ($user, $post) {
            $follow = $event->follow;
            return $follow->followable instanceof Post && $follow->user instanceof User && $follow->user->id == $user->id && $follow->followable->id == $post->id;
        });
    }

    public function test_unfollow_features()
    {
        $user1 = User::create(['name' => 'user1']);
        $user2 = User::create(['name' => 'user2']);
        $user3 = User::create(['name' => 'user3']);
        $post = Post::create(['title' => 'test post']);

        $user1->follow($post);
        $user1->follow($post);
        $user2->follow($post);
        $user3->follow($post);

        $user2->unfollow($post);
        $this->assertFalse($user2->hasFollowed($post));
        $this->assertTrue($user1->hasFollowed($post));
        $this->assertTrue($user3->hasFollowed($post));
        $this->assertCount(1, $user1->follows);
    }

    public function test_aggregations()
    {
        $user = User::create(['name' => 'user']);

        $post1 = Post::create(['title' => 'post1']);
        $post2 = Post::create(['title' => 'post2']);

        $product1 = Product::create(['name' => 'product1']);
        $product2 = Product::create(['name' => 'product2']);

        $user->follow($post1);
        $user->follow($post2);
        $user->follow($product1);
        $user->follow($product2);

        $this->assertCount(4, $user->follows);
        $this->assertCount(2, $user->follows()->withType(Post::class)->get());
    }

    public function test_object_followers()
    {
        $user1 = User::create(['name' => 'user1']);
        $user2 = User::create(['name' => 'user2']);
        $user3 = User::create(['name' => 'user3']);

        $post = Post::create(['title' => 'test post']);

        $user1->follow($post);
        $user2->follow($post);
        $this->assertCount(2, $post->follows);
        $this->assertCount(2, $post->followers);

        $this->assertSame($user1->name, $post->followers[0]['name']);
        $this->assertSame($user2->name, $post->followers[1]['name']);

        $sqls = $this->getQueryLog(function () use ($post, $user1, $user2, $user3) {
            $this->assertTrue($post->isFollowedBy($user1));
            $this->assertTrue($post->isFollowedBy($user2));
            $this->assertFalse($post->isFollowedBy($user3));
        });

        $this->assertEmpty($sqls->all());
    }

    public function test_eager_loading()
    {
        $user = User::create(['name' => 'user']);

        $post1 = Post::create(['title' => 'post1']);
        $post2 = Post::create(['title' => 'post2']);

        $product1 = Product::create(['name' => 'product1']);
        $product2 = Product::create(['name' => 'product2']);

        $user->follow($post1);
        $user->follow($post2);
        $user->follow($product1);
        $user->follow($product2);

        $sqls = $this->getQueryLog(function () use ($user) {
            $user->load('follows.followable');
        });

        $this->assertCount(3, $sqls);

        $sqls = $this->getQueryLog(function () use ($user, $post1) {
            $user->hasFollowed($post1);
        });

        $this->assertEmpty($sqls->all());
    }

    public function test_eager_loading_error()
    {
        $user = User::create(['name' => 'user']);

        $post1 = Post::create(['title' => 'post1']);
        $post2 = Post::create(['title' => 'post2']);

        $user->follow($post2);

        $this->assertFalse($user->hasFollowed($post1));
        $this->assertTrue($user->hasFollowed($post2));

        $user->load('follows');

        $this->assertFalse($user->hasFollowed($post1));
        $this->assertTrue($user->hasFollowed($post2));

        $user1 = User::create(['name' => 'user1']);
        $user2 = User::create(['name' => 'user2']);

        $post = Post::create(['title' => 'Hello world!']);

        $user2->follow($post);

        $this->assertFalse($post->isFollowedBy($user1));
        $this->assertTrue($post->isFollowedBy($user2));

        $post->load('follows');

        $this->assertFalse($post->isFollowedBy($user1));
        $this->assertTrue($post->isFollowedBy($user2));
    }

    public function test_has_followed()
    {
        $user = User::create(['name' => 'user']);
        $post = Post::create(['title' => 'post']);

        $user->follow($post);
        $user->follow($post);
        $user->follow($post);
        $user->follow($post);

        $this->assertTrue($user->hasFollowed($post));
        $this->assertTrue($post->hasBeenFollowedBy($user));
        $this->assertDatabaseCount('follows', 1);

        $user->unfollow($post);
        $this->assertFalse($user->hasFollowed($post));
        $this->assertFalse($post->hasBeenFollowedBy($user));
        $this->assertDatabaseCount('follows', 0);
    }

    protected function getQueryLog(Closure $callback)
    {
        $sqls = collect([]);
        DB::listen(function ($query) use ($sqls) {
            $sqls->push(['sql' => $query->sql, 'bindings' => $query->bindings]);
        });
        $callback();
        return $sqls;
    }
}
