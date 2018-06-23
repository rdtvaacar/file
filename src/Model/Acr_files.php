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
        if (acr_files::where('id', $acr_file_id)->count() == 0) {
            if (!empty($acr_file_id)) {
                $data = [
                    'id' => $acr_file_id,
                    'parent_id' => $session_id,
                ];
            } else {
                $data = ['parent_id' => $session_id];
            }

            return acr_files::insertGetId($data);
        } else {
            return $acr_file_id;
        }
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

    function sil_childs($acr_child_file, $acr_file_id)
    {
        $acr_files_childs_sorgu = Acr_files_childs::where('file_name', $acr_child_file)->where('acr_file_id', $acr_file_id);
        $sil                    = $acr_files_childs_sorgu->delete();
        if (Acr_files_childs::where('acr_file_id', $acr_file_id)->count() == 0) {
            Acr_files::where('id', $acr_file_id)->delete();
        }
        if ($sil) {
            return 1;
        } else {
            return 0;
        }
    }

}
