<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QRCode extends Model
{
    protected $table = 'qr_codes';

    protected $fillable = [
        'data',
        'width',
        'height',
        'logo_path',
        'finder_color',
        'finder_inner_color',
        'data_color',
        'bg_color',
        'generated_image_path',
    ];
}
