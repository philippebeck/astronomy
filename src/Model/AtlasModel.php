<?php

namespace App\Model;

use Pam\Model\MainModel;

/**
 * Class AtlasModel
 * @package App\Model
 */
class AtlasModel extends MainModel
{
    public function listAtlasMaps()
    {
        $query = "SELECT * FROM Map
                INNER JOIN Atlas ON Map.atlas_id = Atlas.id
                ORDER BY Map.map_name";

        return $this->database->getAllData($query);
    }
}
