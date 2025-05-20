<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'file_extension',
        'file_mime',
        'file_original_name',
        'file_original_path',
    ];

    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    public function saveImage($data)
    {
        $this->review_id = $data['review_id'];
        $this->file_name = $data['file_name'];
        $this->file_path = $data['file_path'];
        $this->file_type = $data['file_type'];
        $this->file_size = $data['file_size'];
        $this->file_mime = $data['file_mime'];
        $this->file_extension = $data['file_extension'];
        $this->file_original_name = $data['file_original_name'];
        $this->file_original_path = $data['file_original_path'];
        $this->save();
        return $this;
    }

}
