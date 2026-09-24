<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_id',
        'title',
        'description',
        'start_date',
        'end_date',
    ];

    protected $appends = ['festival_data'];

    public function company()
    {
        return $this->belongsTo(CompanyDetail::class, 'company_id');
    }

    /**
     * Get dynamic festival icon, emoji, colors, and category based on holiday title
     */
    public function getFestivalDataAttribute()
    {
        $title = strtolower($this->title ?? '');

        if (str_contains($title, 'new year')) {
            return [
                'icon' => 'fa-solid fa-champagne-glasses',
                'emoji' => '🎉',
                'color' => '#8b5cf6',
                'bg' => 'linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%)',
                'category' => 'New Year Celebration',
            ];
        }
        if (str_contains($title, 'republic') || str_contains($title, 'gantantra')) {
            return [
                'icon' => 'fa-solid fa-flag',
                'emoji' => '🇮🇳',
                'color' => '#ea580c',
                'bg' => 'linear-gradient(135deg, #f97316 0%, #ea580c 100%)',
                'category' => 'National Holiday',
            ];
        }
        if (str_contains($title, 'independence') || str_contains($title, 'swatantrata')) {
            return [
                'icon' => 'fa-solid fa-flag',
                'emoji' => '🇮🇳',
                'color' => '#ea580c',
                'bg' => 'linear-gradient(135deg, #f97316 0%, #ea580c 100%)',
                'category' => 'National Holiday',
            ];
        }
        if (str_contains($title, 'diwali') || str_contains($title, 'deepavali') || str_contains($title, 'deepawali')) {
            return [
                'icon' => 'fa-solid fa-fire-flame-curved',
                'emoji' => '🪔',
                'color' => '#d97706',
                'bg' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
                'category' => 'Festival of Lights',
            ];
        }
        if (str_contains($title, 'holi') || str_contains($title, 'dhuleti') || str_contains($title, 'rang')) {
            return [
                'icon' => 'fa-solid fa-palette',
                'emoji' => '🎨',
                'color' => '#db2777',
                'bg' => 'linear-gradient(135deg, #f43f5e 0%, #db2777 100%)',
                'category' => 'Festival of Colors',
            ];
        }
        if (str_contains($title, 'christmas') || str_contains($title, 'xmas') || str_contains($title, 'x-mas')) {
            return [
                'icon' => 'fa-solid fa-tree',
                'emoji' => '🎄',
                'color' => '#059669',
                'bg' => 'linear-gradient(135deg, #10b981 0%, #047857 100%)',
                'category' => 'Christmas Celebration',
            ];
        }
        if (str_contains($title, 'eid') || str_contains($title, 'ramadan') || str_contains($title, 'ramzan') || str_contains($title, 'bakrid') || str_contains($title, 'muharram')) {
            return [
                'icon' => 'fa-solid fa-moon',
                'emoji' => '🌙',
                'color' => '#0d9488',
                'bg' => 'linear-gradient(135deg, #14b8a6 0%, #0f766e 100%)',
                'category' => 'Islamic Observance',
            ];
        }
        if (str_contains($title, 'gandhi')) {
            return [
                'icon' => 'fa-solid fa-dove',
                'emoji' => '🕊️',
                'color' => '#475569',
                'bg' => 'linear-gradient(135deg, #64748b 0%, #475569 100%)',
                'category' => 'National Observance',
            ];
        }
        if (str_contains($title, 'rakhi') || str_contains($title, 'raksha') || str_contains($title, 'bhai dooj') || str_contains($title, 'bhai tika')) {
            return [
                'icon' => 'fa-solid fa-gift',
                'emoji' => '🎁',
                'color' => '#e11d48',
                'bg' => 'linear-gradient(135deg, #f43f5e 0%, #be123c 100%)',
                'category' => 'Festive Celebration',
            ];
        }
        if (str_contains($title, 'dussehra') || str_contains($title, 'vijayadashami') || str_contains($title, 'dashahara') || str_contains($title, 'dashmi') || str_contains($title, 'dashami')) {
            return [
                'icon' => 'fa-solid fa-shield-halved',
                'emoji' => '🏹',
                'color' => '#b45309',
                'bg' => 'linear-gradient(135deg, #d97706 0%, #92400e 100%)',
                'category' => 'Vijayadashami / Dussehra',
            ];
        }
        if (str_contains($title, 'janmashtami') || str_contains($title, 'krishna')) {
            return [
                'icon' => 'fa-solid fa-om',
                'emoji' => '🦚',
                'color' => '#2563eb',
                'bg' => 'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)',
                'category' => 'Festive Observance',
            ];
        }
        if (str_contains($title, 'shivratri') || str_contains($title, 'shiva')) {
            return [
                'icon' => 'fa-solid fa-om',
                'emoji' => '🔱',
                'color' => '#0284c7',
                'bg' => 'linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%)',
                'category' => 'Spiritual Observance',
            ];
        }
        if (str_contains($title, 'ganesh') || str_contains($title, 'vinayaka')) {
            return [
                'icon' => 'fa-solid fa-om',
                'emoji' => '🐘',
                'color' => '#ea580c',
                'bg' => 'linear-gradient(135deg, #f97316 0%, #c2410c 100%)',
                'category' => 'Festive Observance',
            ];
        }
        if (str_contains($title, 'durga') || str_contains($title, 'navratri') || str_contains($title, 'navaratri') || str_contains($title, 'puja') || str_contains($title, 'saptami') || str_contains($title, 'astami') || str_contains($title, 'ashtami') || str_contains($title, 'navami') || str_contains($title, 'dashami') || str_contains($title, 'dashmi')) {
            return [
                'icon' => 'fa-solid fa-bell',
                'emoji' => '🌺',
                'color' => '#dc2626',
                'bg' => 'linear-gradient(135deg, #ef4444 0%, #b91c1c 100%)',
                'category' => 'Festive Observance',
            ];
        }
        if (str_contains($title, 'good friday') || str_contains($title, 'easter')) {
            return [
                'icon' => 'fa-solid fa-cross',
                'emoji' => '✝️',
                'color' => '#6366f1',
                'bg' => 'linear-gradient(135deg, #818cf8 0%, #4f46e5 100%)',
                'category' => 'Christian Observance',
            ];
        }
        if (str_contains($title, 'buddha') || str_contains($title, 'guru') || str_contains($title, 'gurpurab') || str_contains($title, 'baisakhi') || str_contains($title, 'vaisakhi') || str_contains($title, 'mahavir')) {
            return [
                'icon' => 'fa-solid fa-dharmachakra',
                'emoji' => '☸️',
                'color' => '#ea580c',
                'bg' => 'linear-gradient(135deg, #f97316 0%, #ea580c 100%)',
                'category' => 'Spiritual Observance',
            ];
        }
        if (str_contains($title, 'sankranti') || str_contains($title, 'pongal') || str_contains($title, 'lohri') || str_contains($title, 'bihu') || str_contains($title, 'chhath') || str_contains($title, 'chhat')) {
            return [
                'icon' => 'fa-solid fa-sun',
                'emoji' => '🪁',
                'color' => '#f59e0b',
                'bg' => 'linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%)',
                'category' => 'Harvest Festival',
            ];
        }
        if (str_contains($title, 'labour') || str_contains($title, 'may day') || str_contains($title, 'worker')) {
            return [
                'icon' => 'fa-solid fa-hammer',
                'emoji' => '⚒️',
                'color' => '#0284c7',
                'bg' => 'linear-gradient(135deg, #38bdf8 0%, #0284c7 100%)',
                'category' => 'International Observance',
            ];
        }
        if (str_contains($title, 'women')) {
            return [
                'icon' => 'fa-solid fa-venus',
                'emoji' => '👩',
                'color' => '#db2777',
                'bg' => 'linear-gradient(135deg, #f472b6 0%, #db2777 100%)',
                'category' => 'Special Observance',
            ];
        }
        if (str_contains($title, 'anniversary') || str_contains($title, 'birthday') || str_contains($title, 'foundation')) {
            return [
                'icon' => 'fa-solid fa-cake-candles',
                'emoji' => '🎂',
                'color' => '#ec4899',
                'bg' => 'linear-gradient(135deg, #f43f5e 0%, #e11d48 100%)',
                'category' => 'Corporate Observance',
            ];
        }

        return [
            'icon' => 'fa-solid fa-umbrella-beach',
            'emoji' => '🏖️',
            'color' => '#059669',
            'bg' => 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
            'category' => 'Company Holiday',
        ];
    }
}