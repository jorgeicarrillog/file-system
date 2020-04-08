<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB as DB;
use \App\Traits\AutoGenerateUuid;
use App\Events\FolderDeleting;

class Folder extends Model
{
    use AutoGenerateUuid;
	//public $with = ['folders' ,'files'];
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

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'deleting' => FolderDeleting::class,
    ];

    public function folders()
    {
        return $this->hasMany('App\Folder', 'parent','id');
    }

    public function parent()
    {
        return $this->belongsTo('App\Folder');
    }

    public function files()
    {
        return $this->hasMany('App\File','parent','id');
    }
}
