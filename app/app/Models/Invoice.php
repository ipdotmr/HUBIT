<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'client_id', 'number', 'status', 'issue_date', 'due_date',
        'subtotal', 'tax', 'discount', 'total', 'paid', 'currency', 'notes'
    ];
    
    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid' => 'decimal:2',
    ];
    
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    
    public function invoice_items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
    
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
