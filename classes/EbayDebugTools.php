<?php

class EbayDebugTools
{
    static public function updateProfileIdentifier($old, $new)
    {
        $return = true;
        $data = [
            'ebay_user_identifier' => $new
        ];

        $return &= Db::getInstance()->update(
            'ebay_profile',
            $data,
            sprintf('ebay_user_identifier LIKE "%s"', $old)
        );

        $return &= Db::getInstance()->update(
            'ebay_user_identifier_token',
                $data,
            sprintf('ebay_user_identifier LIKE "%s"', $old)
        );

        return $return;
    }

    static public function getTableData($table, $where = null)
    {
        $query = (new DbQuery())->from($table);

        if (false === is_null($where)) {
            $query->where($where);
        }

        return Db::getInstance()->executeS($query);
    }
}