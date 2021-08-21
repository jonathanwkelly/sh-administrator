<?php

namespace Terranet\Administrator;

use Cache;
use Carbon\Carbon;
use DateTime;
use Doctrine\DBAL\Schema\MySqlSchemaManager;

class Schema
{
    /**
     * @var MySqlSchemaManager
     */
    private $manager;

    public function __construct($manager)
    {
        $this->manager = $manager;
    }

    /**
     * Get list of indexed columns.
     *
     * @param $table
     *
     * @return array
     */
    public function indexedColumns($table)
    {
        return Cache::remember("{$table}_indexed_columns", 300, function () use ($table) {
            $indexedColumns = array_reduce($this->indexes($table), function ($indexedColumns, $index) {
                return array_merge($indexedColumns, $index->getColumns());
            }, []);

            return array_unique($indexedColumns);
        });
    }

    /**
     * List table indexes.
     *
     * @param $table
     *
     * @return \Doctrine\DBAL\Schema\Index[]
     */
    public function indexes($table)
    {
        return Cache::remember("{$table}_indexes", 300, function () use ($table) {
            return $this->manager->listTableIndexes($table);
        });
    }

    /**
     * List table columns.
     *
     * @param $table
     *
     * @return \Doctrine\DBAL\Schema\Column[]
     */
    public function columns($table)
    {
        return Cache::remember("{$table}_columns", 300, function () use ($table) {
            $columns = $this->manager->listTableColumns($table);
            $keys = array_keys($columns);
            $vals = array_values($columns);

            $keys = array_map(function ($key) {
                return trim(str_replace('`', '', $key));
            }, $keys);

            return array_combine($keys, $vals);
        });
    }

    /**
     * list table foreign keys.
     *
     * @param $table
     *
     * @return mixed
     */
    public function foreignKeys($table)
    {
        return Cache::remember("{$table}_foreign_keys", 300, function () use ($table) {
            return $this->manager->listTableForeignKeys($table);
        });
    }

    /**
     * @return MySqlSchemaManager
     */
    public function getManager()
    {
        return $this->manager;
    }
}
