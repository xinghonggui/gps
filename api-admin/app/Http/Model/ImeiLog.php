<?php

namespace App\Http\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Http\Model\Traits\Timestamp;

class ImeiLog extends Authenticatable
{
	use Notifiable,Timestamp;
	protected $table='imei_log';
}

