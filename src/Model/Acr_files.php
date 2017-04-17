<?php

namespace Acr\File\Model;

use Illuminate\Database\Eloquent\Model;
use Auth;

class acr_files extends Model

{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $options;

    function kaydet($acr_file_id, $session_id)
    {

        if (@acr_files::find($acr_file_id)->id == null) {
            $data = ['session_id' => $session_id];
            return acr_files::insertGetId($data);
        };
    }

    function acr_file_session($acr_file_id)
    {
        @$session_id = acr_files::find($acr_file_id)->session_id;
        if ($session_id) {
            return $session_id;
        } else
            return null;

    }

    function child_fields_create($data)
    {
        Acr_files_childs::insert($data);
    }

}
