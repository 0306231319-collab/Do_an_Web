<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchDisposal extends Model
{
    protected $fillable = ['batch_id','user_id','quantity','reason','image_path'];

    public function batch() { return $this->belongsTo(Batch::class); }
}
