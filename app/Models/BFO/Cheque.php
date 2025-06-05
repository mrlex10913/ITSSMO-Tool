<?php

namespace App\Models\BFO;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cheque extends Model
{
    use HasFactory;

    protected $fillable = [
        'cheque_number',
        'payee_name',
        'amount',
        'amount_in_words',
        'cheque_date',
        'status',
        'printed_at',
        'created_by',
        'field_positions'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'cheque_date' => 'date',
        'printed_at' => 'datetime',
        'field_positions' => 'array'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2);
    }

    public static function generateChequeNumber()
    {
        $year = date('Y');
        $lastCheque = static::whereYear('created_at', $year)->latest()->first();

        if ($lastCheque) {
            $lastNumber = (int) substr($lastCheque->cheque_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'CHQ-' . $year . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
