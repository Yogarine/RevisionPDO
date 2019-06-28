<?php
namespace RevisionPDO;

use DateTime;

class Metadata
{
    const OPERATION_INSERT = 'insert';
    const OPERATION_SELECT = 'select';
    const OPERATION_UPDATE = 'update';
    const OPERATION_DELETE = 'delete';

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $operation;

    /**
     * @var Operator
     */
    public $operator;

    /**
     * @var string[]
     */
    public $tables;

    /**
     * @var \DateTime
     */
    public $dateTime;

    /**
     * @var string
     */
    public $ipAddress;

    /**
     * @param  string        $operation
     * @param  string[]      $tables
     * @param  Operator      $operator
     * @param  DateTime|null $dateTime
     *
     * @throws \Exception
     */
    public function __construct($operation, array $tables, Operator $operator, DateTime $dateTime = null)
    {
        if (null === $dateTime) {
            $dateTime = new DateTime;
        }

        $this->operation = $operation;
        $this->tables    = $tables;
        $this->operator  = $operator;
        $this->dateTime  = $dateTime;
    }

    public function toArray()
    {
        return array(
            'id'                  => $this->id,
            'date_time'           => $this->dateTime->format(DATE_ATOM),
            'operation'           => $this->operation,
            'operator_name'       => $this->operator->name,
            'operator_ip_address' => $this->operator->ipAddress,
        );
    }
}
