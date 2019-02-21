<?php

use PHPUnit\Framework\TestCase;

final class PDOTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testTimeZoneOption()
    {
        $adapter = $this->getAdapter();
        $adapter->expects($this->once())
                ->method('setTimeZone')
                ->with($this->equalTo('UTC'));

        $options = array(
            \RevisionPDO\PDO::ATTR_TIME_ZONE => 'UTC',
        );

        new \RevisionPDO\PDO($adapter, null, null, $options);
    }

    /**
     * @throws \ReflectionException
     */
    public function testInitCommandOption()
    {
        $adapter = $this->getAdapter();
        $adapter->expects($this->once())
            ->method('exec')
            ->with($this->equalTo('test'));

        $options = array(
            \RevisionPDO\PDO::ATTR_INIT_COMMAND => 'test',
        );

        new \RevisionPDO\PDO($adapter, null, null, $options);
    }

    /**
     * @see \RevisionPDO\PDO::prepare()
     * @throws \ReflectionException
     */
    public function testPrepare()
    {
        $adapter = $this->getAdapter();
        /** @noinspection SqlResolve */
        $adapter->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo($statement = 'SELECT * FROM nothing'))
            ->willReturn(new PDOStatement);

        $pdo = new \RevisionPDO\PDO($adapter);
        /** @noinspection PhpParamsInspection */
        $this->assertInstanceOf('\\PDOStatement', $pdo->prepare($statement));
    }

    /**
     * @see \RevisionPDO\PDO::beginTransaction()
     * @throws \ReflectionException
     */
    public function testBeginTransaction()
    {
        $adapter = $this->getAdapter();
        $adapter->expects($this->once())
            ->method('beginTransaction')
            ->willReturn(true);

        $pdo = new \RevisionPDO\PDO($adapter);
        $this->assertTrue($pdo->beginTransaction());
    }

    /**
     * @see \RevisionPDO\PDO::commit()
     * @throws \ReflectionException
     */
    public function testCommit()
    {
        $adapter = $this->getAdapter();
        $adapter->expects($this->once())
            ->method('commit')
            ->willReturn(true);

        $pdo = new \RevisionPDO\PDO($adapter);
        $this->assertTrue($pdo->commit());
    }

    /**
     * @see \RevisionPDO\PDO::rollBack()
     * @throws \ReflectionException
     */
    public function testRollback()
    {
        $adapter = $this->getAdapter();
        $adapter->expects($this->once())
            ->method('rollBack')
            ->willReturn(true);

        $pdo = new \RevisionPDO\PDO($adapter);
        $this->assertTrue($pdo->rollBack());
    }

    /**
     * @see \RevisionPDO\PDO::inTransaction()
     * @throws \ReflectionException
     */
    public function testInTransaction()
    {
        $adapter = $this->getAdapter();
        $adapter->expects($this->once())
            ->method('inTransaction')
            ->willReturn(true);

        $pdo = new \RevisionPDO\PDO($adapter);
        $this->assertTrue($pdo->inTransaction());
    }

    /**
     * @see \RevisionPDO\PDO::setAttribute()
     * @throws \ReflectionException
     */
    public function testSetAttribute()
    {
        $adapter = $this->getAdapter();
        $adapter->expects($this->once())
            ->method('setAttribute')
            ->with(
                $this->equalTo($attribute = PDO::ATTR_AUTOCOMMIT),
                $this->equalTo($value     = PDO::CASE_NATURAL)
            )
            ->willReturn(true);

        $pdo = new \RevisionPDO\PDO($adapter);
        $this->assertTrue($pdo->setAttribute($attribute, $value));
    }

    /**
     * @see \RevisionPDO\PDO::exec()
     * @throws \ReflectionException
     */
    public function testExec()
    {
        $adapter = $this->getAdapter();
        /** @noinspection SqlResolve */
        $adapter->expects($this->once())
            ->method('exec')
            ->with($this->equalTo($statement = 'SELECT * FROM test'))
            ->willReturn(1);

        $pdo = new \RevisionPDO\PDO($adapter);
        $this->assertEquals(1, $pdo->exec($statement));
    }

    /**
     * @see \RevisionPDO\PDO::query()
     * @throws \ReflectionException
     */
    public function testQuery()
    {
        $adapter = $this->getAdapter();
        /** @noinspection SqlResolve */
        $adapter->expects($this->once())
            ->method('query')
            ->with(
                $this->equalTo($statement = 'SELECT * FROM test'),
                $this->equalTo($mode = PDO::ATTR_DEFAULT_FETCH_MODE),
                $this->equalTo($arg3 = null),
                $this->equalTo($ctorargs = array())
            )
            ->willReturn(new PDOStatement);

        $pdo = new \RevisionPDO\PDO($adapter);
        /** @noinspection PhpParamsInspection */
        $this->assertInstanceOf('\\PDOStatement', $pdo->query($statement));
    }

    /**
     * @see \RevisionPDO\PDO::lastInsertId()
     * @throws \ReflectionException
     */
    public function testLastInsertId()
    {
        $adapter = $this->getAdapter();
        $adapter->expects($this->once())
            ->method('lastInsertId')
            ->with(
                $this->equalTo($name = null)
            )
            ->willReturn($lastInsertId = '1234');

        $pdo = new \RevisionPDO\PDO($adapter);
        $this->assertEquals($lastInsertId, $pdo->lastInsertId($name));
    }

    /**
     * @see \RevisionPDO\PDO::errorCode()
     * @throws \ReflectionException
     */
    public function testErrorCode()
    {
        $adapter = $this->getAdapter();
        $adapter->expects($this->once())
            ->method('errorCode')
            ->willReturn($errorCode = '42S02');

        $pdo = new \RevisionPDO\PDO($adapter);
        $this->assertEquals($errorCode, $pdo->errorCode());
    }

    /**
     * @see \RevisionPDO\PDO::errorInfo()
     * @throws \ReflectionException
     */
    public function testErrorInfo()
    {
        $adapter = $this->getAdapter();
        $adapter->expects($this->once())
            ->method('errorInfo')
            ->willReturn($errorInfo = array(
                0 => '42S02',
                1 => '1234',
                2 => 'Test',
            ));

        $pdo = new \RevisionPDO\PDO($adapter);
        $this->assertEquals($errorInfo, $pdo->errorInfo());
    }

    /**
     * @see \RevisionPDO\PDO::getAttribute()
     * @throws \ReflectionException
     */
    public function testGetAttribute()
    {
        $adapter = $this->getAdapter();
        $adapter->expects($this->once())
            ->method('getAttribute')
            ->with(
                $this->equalTo($attribute = PDO::ATTR_TIMEOUT)
            )
            ->willReturn($timeout = 5);

        $pdo = new \RevisionPDO\PDO($adapter);
        $this->assertEquals($timeout, $pdo->getAttribute($attribute));
    }

    /**
     * @see \RevisionPDO\PDO::quote()
     * @throws \ReflectionException
     */
    public function testQuote()
    {
        $adapter = $this->getAdapter();
        $adapter->expects($this->once())
            ->method('quote')
            ->with(
                $this->equalTo($string = "foo"),
                $this->equalTo($parameterType = PDO::PARAM_STR)
            )
            ->willReturn($quote = "'foo'");

        $pdo = new \RevisionPDO\PDO($adapter);
        $this->assertEquals($quote, $pdo->quote($string, $parameterType));
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     * @throws \ReflectionException
     */
    private function getAdapter()
    {
        return $this->getMockBuilder('\\RevisionPDO\\Adapter\\DefaultAdapter')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
