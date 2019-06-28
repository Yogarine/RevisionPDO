<?php

use PHPSQLParser\utils\ExpressionType;
use PHPUnit\Framework\TestCase;
use RevisionPDO\Interpreter;

final class InterpreterTest extends TestCase
{
    public function testGetSubTreesRecursively()
    {
        $origTree = array(
            'SELECT' => array(
                0 => array(
                    'expr_type' => ExpressionType::SUBQUERY,
                    'sub_tree'  => array(
                        'SELECT' => array(
                            0 => array(
                                'expr_type' => ExpressionType::COLREF,
                                'alias'     => null,
                                'base_expr' => 'a',
                                'sub_tree'  => null,
                            ),
                            1 => array(
                                'expr_type' => ExpressionType::SUBQUERY,
                                'sub_tree'  => array(
                                    'SELECT' => array(
                                        0 => array(
                                            'expr_type' => ExpressionType::COLREF,
                                            'alias'     => null,
                                            'base_expr' => 'b',
                                            'sub_tree'  => null,
                                        )
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );

        $interpreter = new Interpreter($origTree);
        $subTrees = $interpreter->getTreesRecursively();

        $this->assertEquals(
            array(
                array(
                    'SELECT' => array(
                        0 => array(
                            'expr_type' => ExpressionType::SUBQUERY,
                            'sub_tree'  => null,
                        )
                    )
                ),
                array(
                    'SELECT' => array(
                        0 => array(
                            'expr_type' => ExpressionType::COLREF,
                            'alias'     => null,
                            'base_expr' => 'a',
                            'sub_tree'  => null,
                        ),
                        1 => array(
                            'expr_type' => ExpressionType::SUBQUERY,
                            'sub_tree'  => null,
                        )
                    )
                ),
                array(
                    'SELECT' => array(
                        0 => array(
                            'expr_type' => ExpressionType::COLREF,
                            'alias'     => null,
                            'base_expr' => 'b',
                            'sub_tree'  => null,
                        )
                    )
                )
            ),
            $subTrees
        );
    }
}
