<?php

namespace Acr\File\Model;

use Illuminate\Database\Eloquent\Model;
use Auth;

class Acr_files_childs extends Model

{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    function plan_id()
    {
        return $this->hasOne('App\Acr_file_child_plan', 'acr_file_child_id', 'id');
    }

    function plan_file()
    {
        return $this->hasOne('App\Plan_file', 'acr_file_id', 'acr_file_id');
    }
}
