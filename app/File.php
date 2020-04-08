<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \App\Traits\AutoGenerateUuid;

class File extends Model
{
	use AutoGenerateUuid;
	public $incrementing = false;
	protected $keyType = 'string';
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'children' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'text', 'parent',
    ];

    public function folder()
    {
        return $this->belongsTo('App\Folder');
    }
}
