<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpertAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'file_path',
        'original_name',
        'attachment_type',
        'file_size',
        'mime_type',
    ];

    /**
     * Allowed attachment types.
     */
    public const TYPES = [
        'شهادة',
        'دورة',
        'وثيقة عمل حر',
        'سجل تجاري',
        'مشهد',
        'تعريف عمل',
        'خبرة',
        'اعتماد',
        'تزكية',
        'أخرى',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getUrlAttribute(): string
    {
        return asset('uploads/experts/attachments/' . $this->file_path);
    }

    public function getIsImageAttribute(): bool
    {
        $ext = strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
    }

    public function getIsPdfAttribute(): bool
    {
        $ext = strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));
        return $ext === 'pdf';
    }
}

