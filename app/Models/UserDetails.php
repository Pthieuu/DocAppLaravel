<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Doctor;
use App\Models\User;
// use App\Models\UserDetails;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class UserDetails extends Model
{
    use HasFactory;

        protected $fillable =[
          'user_id',
          'bio_data',
          'status',
        ];


        public function user(){
          return $this->belongsTo(User::class);
        }
}
