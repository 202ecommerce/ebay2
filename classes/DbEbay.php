<?php

class DbEbay
{
    /** @var  Db */
    private $db;

    public function setDb(Db $db)
    {
        $this->db = $db;
        return $this;
    }

    public function autoExecute($table, $data, $type, $where = '', $limit = 0, $use_cache = true, $use_null = false)
    {
        $type = strtoupper($type);
        switch ($type) {
            case 'INSERT' :
                return $this->db->insert($table, $data, $use_null, $use_cache, Db::INSERT, false);

            case 'INSERT IGNORE' :
                return $this->db->insert($table, $data, $use_null, $use_cache, Db::INSERT_IGNORE, false);

            case 'REPLACE' :
                return $this->db->insert($table, $data, $use_null, $use_cache, Db::REPLACE, false);

            case 'UPDATE' :
                return $this->db->update($table, $data, $where, $limit, $use_null, $use_cache, false);

            default :
                throw new PrestaShopDatabaseException('Wrong argument (miss type) in Db::autoExecute()');
        }
    }
}
