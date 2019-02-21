<?php

namespace RevisionPDO\Adapter;

class MySqlAdapter extends DefaultAdapter
{
    /**
     * @param  string  $timezone
     */
    public function setTimeZone($timezone)
    {
        $this->pdo->exec("SET time_zone = '{$timezone}'");
    }
}