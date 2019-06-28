<?php

namespace RevisionPDO;

use PHPSQLParser\utils\ExpressionType;

class Interpreter
{
    /**
     * Possible operations.
     *
     * @var array
     */
    public static $OPERATIONS = array(
        'SELECT' => Metadata::OPERATION_SELECT,
        'INSERT' => Metadata::OPERATION_INSERT,
        'UPDATE' => Metadata::OPERATION_UPDATE,
        'DELETE' => Metadata::OPERATION_DELETE,
    );

    /**
     * Mapping of sections to what section their tables can be found.
     *
     * @var array
     */
    public static $TABLE_SECTIONS = array(
        'SELECT' => 'FROM',
        'DELETE' => 'FROM',
    );

    /**
     * @var array
     */
    protected $tree;

    /**
     * @param array $tree
     */
    public function __construct(array $tree)
    {
        $this->tree = $tree;
    }

    /**
     * @param  array  $tree
     * @return array
     */
    public function getTreesRecursively(array &$tree = null)
    {
        if (null === $tree) {
            $tree = $this->tree;
        }

        $subTrees[] =& $tree;
        foreach ($tree as $section => &$items) {
            foreach ($items as &$item) {
                if (
                    isset($item['expr_type']) &&
                    ExpressionType::SUBQUERY == $item['expr_type'] &&
                    isset($item['sub_tree']) &&
                    is_array($item['sub_tree']) &&
                    ! empty($item['sub_tree'])
                ) {
                    $subTree = $item['sub_tree'];
                    $item['sub_tree'] = null;

                    $subTrees = array_merge($subTrees, $this->getTreesRecursively($subTree));
                }
            }
        }

        return $subTrees;
    }

    /**
     * @return Interpreter[]
     */
    public function flatten()
    {
        $interpreters = array();

        foreach ($this->getTreesRecursively() as $tree) {
            $interpreters[] = new Interpreter($tree);
        }

        return $interpreters;
    }

    /**
     * @return string|null
     */
    public function getOperation()
    {
        // Get the first operation.
        foreach (self::$OPERATIONS as $section => $operation) {
            if (isset($this->tree[$section])) {
                return $operation;
            }
        }

        return null;
    }

    /**
     * @return string[]
     */
    public function getTables()
    {
        $tables = array();

        foreach (self::$OPERATIONS as $section => $operation) {
            if (isset($this->tree[$section])) {
                $tableSection = isset(self::$TABLE_SECTIONS[$section]) ? self::$TABLE_SECTIONS[$section] : $section;

                if (isset($this->tree[$tableSection])) {
                    foreach ($this->tree[$tableSection] as $expression) {
                        if (ExpressionType::TABLE == $expression['expr_type']) {
                            $tables[] = $expression['table'];
                        }
                    }
                }

                break;
            }
        }

        return $tables;
    }
}
