<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlecchino\DatabaseAbstractionLayer\Tests\SqlBased\Element;

use Arlecchino\Core\Collection\ArrayCollection;
use Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Column;
use Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\PrimaryKey;
use Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Table;
use Arlecchino\Core\Tests\Helper\CommonTestHelper;
use PHPUnit_Framework_TestCase;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class PrimaryKeyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\PrimaryKey::__construct
     */
    public function testConstruct()
    {
        $primaryKey = $this->createBaseNewPrimaryKey();

        $this->assertAttributeSame(
            null,
            'table',
            $primaryKey
        );
        $this->assertAttributeInstanceOf(
            ArrayCollection::class,
            'columns',
            $primaryKey
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\PrimaryKey::getTable
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\PrimaryKey::setTable
     */
    public function testGetAndSetTable()
    {
        CommonTestHelper::testBasicGetAndSetForProperty(
            $this,
            $this->createBaseNewPrimaryKey(),
            'table',
            $this->createBaseNewTable()
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\PrimaryKey::getColumns
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\PrimaryKey::setColumns
     */
    public function testGetAndSetColumns()
    {
        CommonTestHelper::testBasicGetAndSetCollectionForProperty(
            $this,
            $this->createBaseNewPrimaryKey(),
            'columns',
            array(
                $this->createBaseNewColumn()
            )
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\PrimaryKey::addColumn
     */
    public function testAddColumn()
    {
        $primaryKey = $this->createBaseNewPrimaryKey();

        $primaryKey->setColumns(
            array(
                $this->createBaseNewColumn()
            )
        );

        CommonTestHelper::testBasicAddForProperty(
            $this,
            $primaryKey,
            'columns',
            $this->createBaseNewColumn()
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\PrimaryKey::addColumns
     */
    public function testAddColumns()
    {
        $primaryKey = $this->createBaseNewPrimaryKey();

        $primaryKey->setColumns(
            array(
                $this->createBaseNewColumn()
            )
        );

        CommonTestHelper::testBasicAddCollectionForProperty(
            $this,
            $primaryKey,
            'columns',
            array(
                $this->createBaseNewColumn()
            )
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\PrimaryKey::toArray
     */
    public function testToArray()
    {
        $column = $this->createBaseNewColumn();
        $column->setName(
            'deptNo'
        );

        $primaryKey = $this->createBaseNewPrimaryKey();
        $primaryKey->addColumn(
            $column
        );

        $table = $this->createBaseNewTable();
        $table->setName(
            'testTableName'
        )->setPrimaryKey(
            $primaryKey
        )->setColumns(
            array(
                $column
            )
        );

        $arr = $primaryKey->toArray();
        $expected = array(
            'columns' => array(
                'deptNo'
            ),
            'table' => 'testTableName'
        );
        
        $this->assertEquals(
            $expected,
            $arr
        );
    }

    /**
     * @return Column
     */
    protected function createBaseNewColumn()
    {
        return $this->getMockForAbstractClass(
            Column::class
        );
    }

    /**
     * @return PrimaryKey
     */
    protected function createBaseNewPrimaryKey()
    {
        return $this->getMockForAbstractClass(
            PrimaryKey::class
        );
    }

    /**
     * @return Table
     */
    protected function createBaseNewTable()
    {
        return $this->getMockForAbstractClass(
            Table::class
        );
    }
}
