<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Driver\Pdo\MySql\Migration\Tests\Builder;

use Arlekin\Dbal\Driver\Pdo\MySql\DatabaseConnection;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\Column;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\ColumnType;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\ForeignKey;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\ForeignKeyOnDeleteConstraint;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\ForeignKeyOnUpdateConstraint;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\Index;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\IndexKind;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\Table;
use Arlekin\Dbal\Driver\Pdo\MySql\Migration\Builder\SchemaBuilder;
use Arlekin\Dbal\Tests\Driver\Pdo\MySql\AbstractBasePdoMySqlTest;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class SchemaBuilderTest extends AbstractBasePdoMySqlTest
{
    /**
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Migration\Builder\SchemaBuilder::getFromDatabase
     */
    public function testGetFromDatabaseEmptySchema()
    {
        $schemaBuilder = $this->getSchemaBuilder();

        $schema = $schemaBuilder->getFromDatabase(
            $this->getDatabaseConnectionMock(
                function () {
                    $viewResultSet = [];

                    $coreTableResultSet = [];

                    $indexesTableResultSet = [];

                    $fkTableResultSet = [];

                    return [
                        $viewResultSet,
                        $coreTableResultSet,
                        $indexesTableResultSet,
                        $fkTableResultSet,
                    ];
                }
            )
        );

        $this->assertCount(
            0,
            $schema->getTables()
        );
        $this->assertCount(
            0,
            $schema->getViews()
        );
    }

    /**
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Migration\Builder\SchemaBuilder::getFromDatabase
     */
    public function testGetFromDatabaseSimpleView()
    {
        $schemaBuilder = $this->getSchemaBuilder();

        $schema = $schemaBuilder->getFromDatabase(
            $this->getDatabaseConnectionMock(
                function () {
                    $viewResultSet = [
                        [
                            'viewName' => 'testView',
                            'viewDefinition' => 'select `testColumn` from `testTable`',
                        ]
                    ];

                    $coreTableResultSet = [];

                    $indexesTableResultSet = [];

                    $fkTableResultSet = [];

                    return [
                        $viewResultSet,
                        $coreTableResultSet,
                        $indexesTableResultSet,
                        $fkTableResultSet,
                    ];
                }
            )
        );

        $this->assertCount(
            0,
            $schema->getTables()
        );
        $this->assertCount(
            1,
            $schema->getViews()
        );

        $view = $schema->getViews()[0];

        $this->assertSame(
            'testView',
            $view->getName()
        );
        $this->assertSame(
            'select `testColumn` from `testTable`',
            $view->getDefinition()
        );
    }

    /**
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Migration\Builder\SchemaBuilder::getFromDatabase
     */
    public function testGetFromDatabaseSimpleTable()
    {
        $schemaBuilder = $this->getSchemaBuilder();

        $schema = $schemaBuilder->getFromDatabase(
            $this->getDatabaseConnectionMock(
                function () {
                    $viewResultSet = [];

                    $coreTableResultSet = [
                        [
                            'tableName' => 'testTable',
                            'tableType' => 'BASE TABLE',
                            'columnName' => 'id',
                            'columnExtra' => 'auto_increment',
                            'columnType' => 'int(11)',
                            'columnDataType' => 'int',
                            'columnCharacterMaximumLength' => null,
                            'columnNumericPrecision' => 10,
                            'columnNullable' => false,
                        ],
                    ];

                    $indexesTableResultSet = [];

                    $fkTableResultSet = [];

                    return [
                        $viewResultSet,
                        $coreTableResultSet,
                        $indexesTableResultSet,
                        $fkTableResultSet,
                    ];
                }
            )
        );

        $this->assertCount(
            1,
            $schema->getTables()
        );
        $this->assertCount(
            0,
            $schema->getViews()
        );

        $table = $schema->getTables()[0];

        /* @var $table Table */

        $this->assertSame(
            'testTable',
            $table->getName()
        );

        $this->assertCount(
            1,
            $table->getColumns()
        );

        $column = $table->getColumns()[0];

        /* @var $column Column */

        $this->assertSame(
            'id',
            $column->getName()
        );

        $this->assertTrue(
            $column->isAutoIncrement()
        );

        $this->assertSame(
            ColumnType::TYPE_INT,
            $column->getType()
        );

        $this->assertFalse(
            $column->isNullable()
        );

        $this->assertSame(
            [
                'length' => 11,
            ],
            $column->getParameters()
        );
    }

    /**
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Migration\Builder\SchemaBuilder::getFromDatabase
     */
    public function testGetFromDatabaseMoreComplexTable()
    {
        $schemaBuilder = $this->getSchemaBuilder();

        $schema = $schemaBuilder->getFromDatabase(
            $this->getDatabaseConnectionMock(
                function () {
                    $viewResultSet = [];
                
                    $coreTableResultSet = [
                        [
                            'tableName' => 'testTable',
                            'tableType' => 'BASE TABLE',
                            'columnName' => 'id',
                            'columnExtra' => 'auto_increment',
                            'columnType' => 'int(11)',
                            'columnDataType' => 'int',
                            'columnCharacterMaximumLength' => null,
                            'columnNumericPrecision' => 10,
                            'columnNullable' => false,
                        ],
                        [
                            'tableName' => 'testTable',
                            'tableType' => 'BASE TABLE',
                            'columnName' => 'testEnum',
                            'columnExtra' => '',
                            'columnType' => 'enum(\'YES\',\'NO\')',
                            'columnDataType' => 'enum',
                            'columnCharacterMaximumLength' => 3,
                            'columnNumericPrecision' => null,
                            'columnNullable' => true,
                        ],
                        [
                            'tableName' => 'testTable',
                            'tableType' => 'BASE TABLE',
                            'columnName' => 'testVarchar',
                            'columnExtra' => '',
                            'columnType' => 'varchar(255) unsigned',
                            'columnDataType' => 'varchar',
                            'columnCharacterMaximumLength' => 255,
                            'columnNumericPrecision' => null,
                            'columnNullable' => false,
                        ],
                    ];

                    $indexesTableResultSet = [];

                    $fkTableResultSet = [];

                    return [
                        $viewResultSet,
                        $coreTableResultSet,
                        $indexesTableResultSet,
                        $fkTableResultSet,
                    ];
                }
            )
        );

        $this->assertCount(
            1,
            $schema->getTables()
        );
        $this->assertCount(
            0,
            $schema->getViews()
        );

        $table = $schema->getTables()[0];

        /* @var $table Table */

        $this->assertSame(
            'testTable',
            $table->getName()
        );

        $this->assertCount(
            3,
            $table->getColumns()
        );

        $idColumn = $table->getColumns()[0];

        /* @var $idColumn Column */

        $this->assertSame(
            'id',
            $idColumn->getName()
        );

        $this->assertTrue(
            $idColumn->isAutoIncrement()
        );

        $this->assertSame(
            ColumnType::TYPE_INT,
            $idColumn->getType()
        );

        $this->assertFalse(
            $idColumn->isNullable()
        );

        $this->assertSame(
            [
                'length' => 11,
            ],
            $idColumn->getParameters()
        );

        $testEnumColumn = $table->getColumns()[1];

        /* @var $testEnumColumn Column */

        $this->assertSame(
            'testEnum',
            $testEnumColumn->getName()
        );

        $this->assertFalse(
            $testEnumColumn->isAutoIncrement()
        );

        $this->assertSame(
            ColumnType::TYPE_ENUM,
            $testEnumColumn->getType()
        );

        $this->assertFalse(
            $testEnumColumn->isNullable()
        );

        $this->assertSame(
            [
                'allowedValues' => [
                    'YES',
                    'NO',
                ],
            ],
            $testEnumColumn->getParameters()
        );

        $testVarcharColumn = $table->getColumns()[2];

        /* @var $testVarcharColumn Column */

        $this->assertSame(
            'testVarchar',
            $testVarcharColumn->getName()
        );

        $this->assertFalse(
            $testVarcharColumn->isAutoIncrement()
        );

        $this->assertSame(
            ColumnType::TYPE_VARCHAR,
            $testVarcharColumn->getType()
        );

        $this->assertFalse(
            $testVarcharColumn->isNullable()
        );

        $this->assertSame(
            [
                'length' => 255,
            ],
            $testVarcharColumn->getParameters()
        );
    }

    /**
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Migration\Builder\SchemaBuilder::getFromDatabase
     */
    public function testGetFromDatabaseSimpleTableWithIndexUnique()
    {
        $schemaBuilder = $this->getSchemaBuilder();

        $schema = $schemaBuilder->getFromDatabase(
            $this->getDatabaseConnectionMock(
                function () {
                    $viewResultSet = [];

                    $coreTableResultSet = [
                        [
                            'tableName' => 'testTable',
                            'tableType' => 'BASE TABLE',
                            'columnName' => 'id',
                            'columnExtra' => 'auto_increment',
                            'columnType' => 'int(11)',
                            'columnDataType' => 'int',
                            'columnCharacterMaximumLength' => null,
                            'columnNumericPrecision' => 10,
                            'columnNullable' => false,
                        ],
                    ];

                    $indexesTableResultSet = [
                        [
                            'tableType' => 'BASE TABLE',
                            'tableName' => 'testTable',
                            'statNonUnique' => '0',
                            'statIndexName' => 'testIndex',
                            'statColumnName' => 'id',
                            'statIndexType' => '',
                        ],
                    ];

                    $fkTableResultSet = [];

                    return [
                        $viewResultSet,
                        $coreTableResultSet,
                        $indexesTableResultSet,
                        $fkTableResultSet,
                    ];
                }
            )
        );

        $this->assertCount(
            1,
            $schema->getTables()
        );
        $this->assertCount(
            0,
            $schema->getViews()
        );

        $table = $schema->getTables()[0];

        /* @var $table Table */

        $this->assertSame(
            'testTable',
            $table->getName()
        );

        $this->assertCount(
            1,
            $table->getColumns()
        );

        $column = $table->getColumns()[0];

        /* @var $column Column */

        $this->assertSame(
            'id',
            $column->getName()
        );

        $this->assertTrue(
            $column->isAutoIncrement()
        );

        $this->assertSame(
            ColumnType::TYPE_INT,
            $column->getType()
        );

        $this->assertFalse(
            $column->isNullable()
        );

        $this->assertSame(
            [
                'length' => 11,
            ],
            $column->getParameters()
        );

        $index = $table->getIndexes()[0];

        /* @var $index Index */

        $this->assertSame(
            'testIndex',
            $index->getName()
        );

        $this->assertSame(
            IndexKind::KIND_UNIQUE,
            $index->getKind()
        );

        $this->assertSame(
            [
                $column,
            ],
            $index->getColumns()
        );
    }

    /**
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Migration\Builder\SchemaBuilder::getFromDatabase
     */
    public function testGetFromDatabaseSimpleTableWithIndexNonUnique()
    {
        $schemaBuilder = $this->getSchemaBuilder();

        $schema = $schemaBuilder->getFromDatabase(
            $this->getDatabaseConnectionMock(
                function () {
                    $viewResultSet = [];

                    $coreTableResultSet = [
                        [
                            'tableName' => 'testTable',
                            'tableType' => 'BASE TABLE',
                            'columnName' => 'id',
                            'columnExtra' => 'auto_increment',
                            'columnType' => 'int(11)',
                            'columnDataType' => 'int',
                            'columnCharacterMaximumLength' => null,
                            'columnNumericPrecision' => 10,
                            'columnNullable' => false,
                        ],
                    ];

                    $indexesTableResultSet = [
                        [
                            'tableType' => 'BASE TABLE',
                            'tableName' => 'testTable',
                            'statNonUnique' => '1',
                            'statIndexName' => 'testIndex',
                            'statColumnName' => 'id',
                            'statIndexType' => 'BTREE',
                        ],
                    ];

                    $fkTableResultSet = [];

                    return [
                        $viewResultSet,
                        $coreTableResultSet,
                        $indexesTableResultSet,
                        $fkTableResultSet,
                    ];
                }
            )
        );

        $this->assertCount(
            1,
            $schema->getTables()
        );
        $this->assertCount(
            0,
            $schema->getViews()
        );

        $table = $schema->getTables()[0];

        /* @var $table Table */

        $this->assertSame(
            'testTable',
            $table->getName()
        );

        $this->assertCount(
            1,
            $table->getColumns()
        );

        $column = $table->getColumns()[0];

        /* @var $column Column */

        $this->assertSame(
            'id',
            $column->getName()
        );

        $this->assertTrue(
            $column->isAutoIncrement()
        );

        $this->assertSame(
            ColumnType::TYPE_INT,
            $column->getType()
        );

        $this->assertFalse(
            $column->isNullable()
        );

        $this->assertSame(
            [
                'length' => 11,
            ],
            $column->getParameters()
        );

        $index = $table->getIndexes()[0];

        /* @var $index Index */

        $this->assertSame(
            'testIndex',
            $index->getName()
        );

        $this->assertSame(
            IndexKind::KIND_BTREE,
            $index->getKind()
        );

        $this->assertSame(
            [
                $column,
            ],
            $index->getColumns()
        );
    }

    /**
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Migration\Builder\SchemaBuilder::getFromDatabase
     */
    public function testGetFromDatabaseSimpleTableWithPrimaryKey()
    {
        $schemaBuilder = $this->getSchemaBuilder();

        $schema = $schemaBuilder->getFromDatabase(
            $this->getDatabaseConnectionMock(
                function () {
                    $viewResultSet = [];

                    $coreTableResultSet = [
                        [
                            'tableName' => 'testTable',
                            'tableType' => 'BASE TABLE',
                            'columnName' => 'id',
                            'columnExtra' => 'auto_increment',
                            'columnType' => 'int(11)',
                            'columnDataType' => 'int',
                            'columnCharacterMaximumLength' => null,
                            'columnNumericPrecision' => 10,
                            'columnNullable' => false,
                        ],
                    ];

                    $indexesTableResultSet = [
                        [
                            'tableType' => 'BASE TABLE',
                            'tableName' => 'testTable',
                            'statNonUnique' => '0',
                            'statIndexName' => 'PRIMARY',
                            'statColumnName' => 'id',
                            'statIndexType' => '',
                        ],
                    ];

                    $fkTableResultSet = [];

                    return [
                        $viewResultSet,
                        $coreTableResultSet,
                        $indexesTableResultSet,
                        $fkTableResultSet,
                    ];
                }
            )
        );

        $this->assertCount(
            1,
            $schema->getTables()
        );
        $this->assertCount(
            0,
            $schema->getViews()
        );

        $table = $schema->getTables()[0];

        /* @var $table Table */

        $this->assertSame(
            'testTable',
            $table->getName()
        );

        $this->assertCount(
            1,
            $table->getColumns()
        );

        $column = $table->getColumns()[0];

        /* @var $column Column */

        $this->assertSame(
            'id',
            $column->getName()
        );

        $this->assertTrue(
            $column->isAutoIncrement()
        );

        $this->assertSame(
            ColumnType::TYPE_INT,
            $column->getType()
        );

        $this->assertFalse(
            $column->isNullable()
        );

        $this->assertSame(
            [
                'length' => 11,
            ],
            $column->getParameters()
        );

        $primaryKey = $table->getPrimaryKey();

        $this->assertSame(
            [
                $column,
            ],
            $primaryKey->getColumns()
        );
    }

    /**
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Migration\Builder\SchemaBuilder::getFromDatabase
     */
    public function testGetFromDatabaseTwoTablesWithForeignKey()
    {
        $schemaBuilder = $this->getSchemaBuilder();

        $schema = $schemaBuilder->getFromDatabase(
            $this->getDatabaseConnectionMock(
                function () {
                    $viewResultSet = [];

                    $coreTableResultSet = [
                        [
                            'tableName' => 'testTable',
                            'tableType' => 'BASE TABLE',
                            'columnName' => 'id',
                            'columnExtra' => 'auto_increment',
                            'columnType' => 'int(11)',
                            'columnDataType' => 'int',
                            'columnCharacterMaximumLength' => null,
                            'columnNumericPrecision' => 10,
                            'columnNullable' => false,
                        ],
                        [
                            'tableName' => 'testTable2',
                            'tableType' => 'BASE TABLE',
                            'columnName' => 'id',
                            'columnExtra' => 'auto_increment',
                            'columnType' => 'int(11)',
                            'columnDataType' => 'int',
                            'columnCharacterMaximumLength' => null,
                            'columnNumericPrecision' => 10,
                            'columnNullable' => false,
                        ],
                    ];

                    $indexesTableResultSet = [];

                    $fkTableResultSet = [
                        [
                            'constraintName' => 'testConstraint',
                            'tableName' => 'testTable',
                            'columnName' => 'id',
                            'referencedTableName' => 'testTable2',
                            'referencedColumnName' => 'id',
                            'deleteRule' => 'CASCADE',
                            'updateRule' => 'RESTRICT',
                        ],
                    ];

                    return [
                        $viewResultSet,
                        $coreTableResultSet,
                        $indexesTableResultSet,
                        $fkTableResultSet,
                    ];
                }
            )
        );

        $this->assertCount(
            2,
            $schema->getTables()
        );
        $this->assertCount(
            0,
            $schema->getViews()
        );

        $table = $schema->getTables()[0];
        $table2 = $schema->getTables()[1];

        /* @var $table Table */
        /* @var $table2 Table */

        $this->assertSame(
            'testTable',
            $table->getName()
        );
        $this->assertSame(
            'testTable2',
            $table2->getName()
        );

        $this->assertCount(
            1,
            $table->getColumns()
        );
        $this->assertCount(
            1,
            $table2->getColumns()
        );

        $idColumn = $table->getColumns()[0];
        $idColumnTable2 = $table2->getColumns()[0];

        /* @var $idColumn Column */
        /* @var $idColumnTable2 Column */

        $this->assertCount(
            1,
            $table->getForeignKeys()
        );
        $this->assertCount(
            0,
            $table2->getForeignKeys()
        );

        $foreignKey = $table->getForeignKeys()[0];
        /* @var $foreignKey ForeignKey */

        $this->assertSame(
            $table2,
            $foreignKey->getReferencedTable()
        );

        $this->assertSame(
            [
                $idColumnTable2,
            ],
            $foreignKey->getReferencedColumns()
        );

        $this->assertSame(
            $table,
            $foreignKey->getTable()
        );

        $this->assertSame(
            [
                $idColumn,
            ],
            $foreignKey->getColumns()
        );

        $this->assertSame(
            ForeignKeyOnDeleteConstraint::ON_DELETE_CASCADE,
            $foreignKey->getOnDelete()
        );

        $this->assertSame(
            ForeignKeyOnUpdateConstraint::ON_UPDATE_RESTRICT,
            $foreignKey->getOnUpdate()
        );
    }

    protected function getDatabaseConnectionMock(callable $executeQueryReturnCallback)
    {
        $mock = $this->getMock(
            DatabaseConnection::class,
            [],
            [],
            '',
            false
        );

        $mock->method(
            'executeMultipleQueries'
        )->will(
            $this->returnCallback(
                $executeQueryReturnCallback
            )
        );

        return $mock;
    }

    /**
     * @return SchemaBuilder
     */
    protected function getSchemaBuilder()
    {
        return new SchemaBuilder();
    }
}