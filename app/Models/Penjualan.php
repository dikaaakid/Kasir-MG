<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualan';
    protected $primaryKey = 'id_penjualan';
    public $fillable = [
        'id_penjualan',
        'id_member',
        'total_item',
        'total_harga',
        'bayar',
        'diterima',
        'id_user',
        'kode_penjualan',
    ];

    protected $guarded = [];

    protected static function booted()
    {
        static::created(function ($penjualan) {
            $kode = 'TRX-' . date('Ymd') . '-' . str_pad($penjualan->kode_penjualan, 4, '0', STR_PAD_LEFT);
            $penjualan->kode_penjualan = $kode;
            $penjualan->save();
        });
    }

    public function member()
    {
        return $this->hasOne(Member::class, 'id_member', 'id_member');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'id_user');
    }
}
