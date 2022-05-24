<?php namespace Anomaly\ImageFieldType;

use Anomaly\Streams\Platform\Addon\FieldType\FieldType;
use Anomaly\Streams\Platform\Addon\FieldType\FieldTypeSchema;
use Anomaly\Streams\Platform\Assignment\Contract\AssignmentInterface;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class ImageFieldTypeSchema
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class ImageFieldTypeSchema extends FieldTypeSchema
{

    /**
     * Add the field type columns.
     *
     * @param Blueprint           $table
     * @param AssignmentInterface $assignment
     */
    public function addColumn(Blueprint $table, AssignmentInterface $assignment)
    {
        $nullable = !$assignment->isTranslatable() ? !$assignment->isRequired() : true;

        $table->integer($this->fieldType->getColumnName())->nullable($nullable);
        $table->text($this->fieldType->getField() . '_data')->nullable(true);

        if ($assignment->isUnique() && !$assignment->isTranslatable()) {
            $table->unique(
                $this->fieldType->getColumnName(),
                md5('unique_' . $this->fieldType->getColumnName())
            );
        }
    }

    /**
     * Rename the field type columns.
     *
     * @param Blueprint $table
     * @param FieldType $from
     */
    public function renameColumn(Blueprint $table, FieldType $from)
    {
        $table->renameColumn($from->getColumnName(), $this->fieldType->getColumnName());
        $table->renameColumn($from->getField() . '_data', $this->fieldType->getField() . '_data');
    }

    /**
     * Update an existing column.
     *
     * @param Blueprint           $table
     * @param AssignmentInterface $assignment
     */
    public function updateColumn(Blueprint $table, AssignmentInterface $assignment)
    {
        $nullable = !$assignment->isTranslatable() ? !$assignment->isRequired() : true;

        $table->integer($this->fieldType->getColumnName())->nullable($nullable)->change();
        $table->text($this->fieldType->getField() . '_data')->nullable(true)->change();

        /**
         * Mark the column unique if desired and not translatable.
         * Otherwise, drop the unique index.
         */
        $connection = $this->schema->getConnection();
        $manager    = $connection->getDoctrineSchemaManager();
        $doctrine   = $manager->listTableDetails($connection->getTablePrefix() . $table->getTable());

        // The unique index name.
        $unique = md5('unique_' . $this->fieldType->getColumnName());

        /**
         * If the assignment is unique and not translatable
         * and the table does not already have the given the
         * given table index then go ahead and add it.
         */
        if ($assignment->isUnique() && !$assignment->isTranslatable() && !$doctrine->hasIndex($unique)) {
            $table->unique($this->fieldType->getColumnName(), $unique);
        }

        /**
         * If the assignment is NOT unique and not translatable
         * and the table DOES have the given table index
         * then we need to remove.
         */
        if (!$assignment->isUnique() && !$assignment->isTranslatable() && $doctrine->hasIndex($unique)) {
            $table->dropIndex($unique);
        }
    }

    /**
     * Drop the field type columns.
     *
     * @param Blueprint $table
     */
    public function dropColumn(Blueprint $table)
    {
        $table->dropColumn($this->fieldType->getColumnName());
        $table->dropColumn($this->fieldType->getField() . '_data');
    }
}
