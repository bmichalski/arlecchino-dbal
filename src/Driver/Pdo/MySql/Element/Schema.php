<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Driver\Pdo\MySql\Element;

/**
 * Represents a MySQL database.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class Schema
{
    /**
     * The schema's tables.
     *
     * @var array
     */
    protected $tables;

    /**
     * The schema's views.
     *
     * @var array
     */
    protected $views;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->tables = [];
        $this->views = [];
    }

    /**
     * Gets the schema's tables.
     *
     * @return array
     */
    public function getTables()
    {
        return $this->tables;
    }

    /**
     * Sets the schema's tables.
     *
     * @param array $tables
     *
     * @return Schema
     */
    public function setTables(array $tables)
    {
        $this->tables = $tables;

        return $this;
    }

    /**
     * @param Table $table
     *
     * @return Schema
     */
    public function addTable(Table $table)
    {
        $this->tables[] = $table;

        return $this;
    }

    /**
     * @param int $index
     *
     * @return Schema
     */
    public function removeTableAtIndex($index)
    {
        unset($this->tables[$index]);

        return $this;
    }

    /**
     * Gets the schema's views.
     *
     * @return array
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Sets the schema's views.
     *
     * @param array $views
     *
     * @return Schema
     */
    public function setViews(array $views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * @param View $view
     *
     * @return Schema
     */
    public function addView(View $view)
    {
        $this->views[] = $view;

        return $this;
    }

    /**
     * @param int $index
     *
     * @return Schema
     */
    public function removeViewAtIndex($index)
    {
        unset($this->views[$index]);

        return $this;
    }

    /**
     * Converts a Schema into an array.
     *
     * @todo Move the toArray responsability away from the Schemas
     *
     * @return array
     */
    public function toArray()
    {
        $tables = $this->getTables();
        $views = $this->getViews();

        $arr = [
            'tables' => [],
            'views' => [],
        ];

        foreach ($tables as $table) {
            /* @var $table Table */
            $arr['tables'][] = $table->toArray();
        }

        foreach ($views as $view) {
            /* @var $view Views */
            $arr['views'][] = $view->toArray();
        }

        return $arr;
    }
}