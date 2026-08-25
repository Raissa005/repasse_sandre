<?php

namespace RR\libs;

use RR\model\ModelGenerico;

class DeleteFile
{
    public static function deleteFile($ids, $table, $paths)
    {
        $modelGenerico = new ModelGenerico();
        foreach ($paths as $path) {
            @unlink($path);
        }
        foreach ($ids as $id) {
            $modelGenerico->deleteItemByCampoGenerico($table, "id", $id);
        }
    }
}
