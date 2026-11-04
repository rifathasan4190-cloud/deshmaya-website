<?php

namespace App\Models;

use Database\Factories\QuoteRequestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['name', 'company', 'email', 'phone', 'country', 'product_id', 'product_name', 'quantity', 'unit', 'message', 'status', 'ip', 'user_agent'])]
class QuoteRequest extends Model
{
    public const STATUS_NEW = 'new';

    public const STATUS_REVIEWING = 'reviewing';

    public const STATUS_QUOTED = 'quoted';

    public const STATUS_CLOSED = 'closed';

    /** @use HasFactory<QuoteRequestFactory> */
    use HasFactory;

    protected $attributes = [
        'status' => self::STATUS_NEW,
    ];

    protected function casts(): array
    {
        return [
            'product_id' => 'integer',
            'quantity' => 'decimal:3',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
