<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matkul extends Model
{
    use HasFactory;

    protected $table = 'matkul'; // Pastikan ini nama tabel kelas Anda
    protected $primaryKey = 'kode_matkul'; // Atau 'kode_kelas' jika itu primary key Anda
    public $incrementing = false; // Set false jika primaryKey non-incrementing
    protected $keyType = 'string'; // Atau 'string' jika kode_kelas string

    protected $fillable = [
        // Daftar kolom yang bisa diisi secara massal
        'kode_matkul',
        'nama_matkul',
        'sks',
    ];
}