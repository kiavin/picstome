<?php

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('returns true when team has lifetime_at set', function () {
    $team = Team::create([
        'user_id' => $this->user->id,
        'name' => 'Test Studio',
        'personal_team' => true,
        'lifetime_at' => now()->subDay(),
    ]);

    expect($team->subscribed())->toBeTrue();
});

it('returns false when team has no lifetime_at and no subscription', function () {
    $team = Team::create([
        'user_id' => $this->user->id,
        'name' => 'Test Studio',
        'personal_team' => true,
        'lifetime_at' => null,
    ]);

    expect($team->subscribed())->toBeFalse();
});

it('returns true when team has no lifetime_at but has an active subscription', function () {
    $team = Team::create([
        'user_id' => $this->user->id,
        'name' => 'Test Studio',
        'personal_team' => true,
        'lifetime_at' => null,
    ]);

    DB::table('subscriptions')->insert([
        'team_id' => $team->id,
        'type' => 'default',
        'stripe_id' => 'sub_test_'.$team->id,
        'stripe_status' => 'active',
        'stripe_price' => 'price_test',
        'quantity' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    expect($team->fresh()->subscribed())->toBeTrue();
});

it('returns false when team has no lifetime_at and subscription is canceled', function () {
    $team = Team::create([
        'user_id' => $this->user->id,
        'name' => 'Test Studio',
        'personal_team' => true,
        'lifetime_at' => null,
    ]);

    DB::table('subscriptions')->insert([
        'team_id' => $team->id,
        'type' => 'default',
        'stripe_id' => 'sub_test_canceled_'.$team->id,
        'stripe_status' => 'canceled',
        'stripe_price' => 'price_test',
        'quantity' => 1,
        'ends_at' => now()->subDay(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    expect($team->fresh()->subscribed())->toBeFalse();
});
