<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str; # Str is just Laravel’s helper for working with text (strings). It helps you do things like cut, clean, or make random text easily.

class Branch extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'type', 'province_id', 'district_id', 'is_active'];
    protected $casts = [
        'province_id' => 'integer',
        'district_id' => 'integer',
        'is_active'   => 'boolean',
    ];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }
    public function district()
    {
        return $this->belongsTo(District::class);
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function stockLevels()
    {
        return $this->hasMany(StockLevel::class);
    }
    // Auto-generate unique code on creationin 
    protected static function booted(): void
    {
        static::creating(function (Branch $branch) {
            if (blank($branch->code)) {
                // Human-friendly prefix from name
                $prefix = 'BR-' . strtoupper(
                    Str::of($branch->name)->replaceMatches('/[^A-Za-z0-9]/', '')->substr(0, 3)
                );

                // Suffix that avoids race conditions (short ULID tail)
                do {
                    $suffix = strtoupper(substr(Str::ulid(), -6)); // e.g., 6 chars
                    $branch->code = "{$prefix}-{$suffix}";
                } while (self::where('code', $branch->code)->exists());
            }
        });
    }
}
