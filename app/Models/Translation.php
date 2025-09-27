<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_text',
        'source_language',
        'target_language',
        'translated_text',
        'audio_file_path',
        'voice_type',
        'pitch',
        'speed',
        'ip_address'
    ];

    protected $casts = [
        'pitch' => 'decimal:1',
        'speed' => 'decimal:1',
    ];

    public static function getLanguages()
    {
        return [
            'en' => 'English',
            'es' => 'Spanish',
            'fr' => 'French',
            'de' => 'German',
            'it' => 'Italian',
            'pt' => 'Portuguese',
            'ru' => 'Russian',
            'ja' => 'Japanese',
            'ko' => 'Korean',
            'zh' => 'Chinese'
        ];
    }
}