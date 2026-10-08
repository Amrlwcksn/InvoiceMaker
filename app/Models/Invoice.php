<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'invoice_date',
        'due_date',
        'subtotal',
        'discount',
        'tax',
        'total',
        'status',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    public function getFormattedDiscountAttribute(): string
    {
        return 'Rp ' . number_format($this->discount, 0, ',', '.');
    }

    public function getFormattedTaxAttribute(): string
    {
        return 'Rp ' . number_format($this->tax, 0, ',', '.');
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'bg-zinc-100 text-zinc-600 border-zinc-200 font-bold',
            'unpaid' => 'bg-amber-50 text-amber-900 border-amber-300 font-bold',
            'paid' => 'bg-emerald-50 text-emerald-900 border-emerald-300 font-bold',
            'overdue' => 'bg-rose-50 text-rose-900 border-rose-300 font-bold',
            default => 'bg-zinc-100 text-zinc-600 border-zinc-200 font-bold',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'unpaid' => 'Belum Dibayar',
            'paid' => 'Lunas',
            'overdue' => 'Jatuh Tempo',
            default => ucfirst($this->status),
        };
    }

    public function getStampImageAttribute(): string
    {
        return match ($this->status) {
            'paid' => 'Lunas.png',
            'unpaid' => 'BelumLunas.png',
            'draft' => 'BelumLunas.png',
            'overdue' => 'JatuhTempo.png',
            default => 'BelumLunas.png',
        };
    }

    public function getStampDetailsAttribute(): array
    {
        return match ($this->status) {
            'paid' => [
                'text' => 'LUNAS',
                'text_class' => 'text-rose-500',
                'border_class' => 'border-rose-400',
                'hex' => '#ef4444',
                'hex_border' => '#f87171',
            ],
            'unpaid' => [
                'text' => 'BELUM LUNAS',
                'text_class' => 'text-rose-500',
                'border_class' => 'border-rose-400',
                'hex' => '#ef4444',
                'hex_border' => '#f87171',
            ],
            'draft' => [
                'text' => 'DRAFT',
                'text_class' => 'text-amber-500',
                'border_class' => 'border-amber-400',
                'hex' => '#f59e0b',
                'hex_border' => '#fbbf24',
            ],
            'overdue' => [
                'text' => 'JATUH TEMPO',
                'text_class' => 'text-rose-700',
                'border_class' => 'border-rose-600',
                'hex' => '#be123c',
                'hex_border' => '#e11d48',
            ],
            default => [
                'text' => strtoupper((string)$this->status),
                'text_class' => 'text-zinc-500',
                'border_class' => 'border-zinc-400',
                'hex' => '#71717a',
                'hex_border' => '#a1a1aa',
            ],
        };
    }

    /**
     * Generate sequential invoice number (e.g. INV-2026-001)
     */
    public static function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $prefix = "INV-{$year}-";
        
        $latest = static::where('invoice_number', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if (!$latest) {
            return $prefix . '001';
        }

        $lastNumber = (int) substr($latest->invoice_number, -3);
        $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        return $prefix . $nextNumber;
    }
}
