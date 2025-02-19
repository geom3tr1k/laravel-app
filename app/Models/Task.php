<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Task extends Model
{
    use HasFactory, Notifiable;

    
   protected $fillable = [
       'title',
       'description',
       'ctreator',
   ];

   public function creator()
   {
       return $this->belongsTo(User::class, 'creator');
   }

   public function users()
   {
       return $this->belongsToMany(User::class, 'task_user');
   }
}
