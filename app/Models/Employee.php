<?php

namespace App\Models;

use App\Enums\EmployerExpenseType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class Employee extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'firstname',
        'lastname',
        'phonenumber',
        'address',
        'position',
        'salary',
    ];

    public function scopeName(Builder $query, $value): Builder
    {
        return $query->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", "%$value%");
    }

    // public function scopeAddress(Builder $query, $value): Builder
    // {
    //   $addresses = Address::getAddressMap();
    //   return $query->where('address', $addresses[$value]);
    // }

    // public function scopePosition(Builder $query, $value): Builder
    // {
    //   $positions = Position::getPositionMap();
    //   return $query->where('position', $positions[$value]);
    // }

    public function rewards($month, $year): HasMany
    {
        return $this->hasMany(Additional::class)->where('type', 'reward')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year);
    }

    public function deductions($month, $year): HasMany
    {
        return $this->hasMany(Additional::class)->where('type', 'deduction')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year);
    }
}