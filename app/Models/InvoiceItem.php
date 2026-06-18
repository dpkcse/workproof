<?php
namespace App\Models;use App\Models\Concerns\BelongsToWorkspace;use Illuminate\Database\Eloquent\Model;
class InvoiceItem extends Model{use BelongsToWorkspace;protected $fillable=['workspace_id','invoice_id','description','quantity','unit_price','total','metadata'];protected function casts():array{return ['metadata'=>'array','quantity'=>'decimal:2','unit_price'=>'decimal:2','total'=>'decimal:2'];}public function invoice(){return $this->belongsTo(Invoice::class);}}
