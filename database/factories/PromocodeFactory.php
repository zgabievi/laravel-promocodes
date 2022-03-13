<?php

namespace Zorb\Promocodes\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Zorb\Promocodes\Models\Promocode;
use Carbon\CarbonInterface;

/**
 * @extends Factory<Promocode>
 */
class PromocodeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Model|Promocode>
     */
    protected $model = Promocode::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->text(9),
            'usages_left' => $this->faker->numberBetween(0, 100),
            'bound_to_user' => $this->faker->boolean,
            'multi_use' => $this->faker->boolean,
            'details' => [
                'discount' => $this->faker->randomFloat(2, 0, 100),
            ],
            'expired_at' => $this->faker->optional()->dateTime('+2 months'),
        ];
    }

    /**
     * @param bool|null $state
     * @return static
     */
    public function boundToUser(bool $state = true): static
    {
        return $this->state(function (array $attributes) use ($state) {
            return [
                'bound_to_user' => $state,
            ];
        });
    }

    /**
     * @param bool|null $state
     * @return static
     */
    public function multiUse(bool $state = true): static
    {
        return $this->state(function (array $attributes) use ($state) {
            return [
                'multi_use' => $state,
            ];
        });
    }

    /**
     * @return static
     */
    public function expired(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'expired_at' => now()->subMinute(),
            ];
        });
    }

    /**
     * @return static
     */
    public function notExpired(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'expired_at' => null,
            ];
        });
    }

    /**
     * @param string $state
     * @return static
     */
    public function code(string $state): static
    {
        return $this->state(function (array $attributes) use ($state) {
            return [
                'code' => $state,
            ];
        });
    }

    /**
     * @param int $state
     * @return static
     */
    public function usagesLeft(int $state = -1): static
    {
        return $this->state(function (array $attributes) use ($state) {
            return [
                'usages_left' => $state,
            ];
        });
    }

    /**
     * @param array $state
     * @return static
     */
    public function details(array $state = []): static
    {
        return $this->state(function (array $attributes) use ($state) {
            return [
                'details' => $state,
            ];
        });
    }
}
