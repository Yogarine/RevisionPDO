<?php

namespace RevisionPDO\Adapter;

class PostgresAdapter extends DefaultAdapter
{
    /**
     * @param  string  $timezone
     */
    public function setTimeZone($timezone)
    {
        $this->pdo->exec("SET TIME ZONE '{$timezone}'");
    }
}