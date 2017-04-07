<?php

namespace Gabievi\Promocodes\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Promocode extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'reward',
        'is_used',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_used' => 'boolean',
        'data' => 'array',
    ];

    /**
     * Promocode constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('promocodes.table', 'promocodes');
    }

    /**
     * Get the user who owns the promocode.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Query builder to find promocode using code.
     *
     * @param $query
     * @param $code
     *
     * @return mixed
     */
    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    /**
     * Query builder to find all not used promocodes.
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeFresh($query)
    {
        return $query->where('is_used', false);
    }
}
