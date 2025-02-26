<?php

namespace Mautic\CoreBundle\Doctrine;

use Doctrine\DBAL\Schema\Schema;

trait TranslationMigrationTrait
{
    /**
     * Add translation parent/child relationship columns.
     */
    protected function addTranslationSchema(Schema $schema, $tableName, $languageColumnName = 'lang')
    {
        $fkName    = $this->generatePropertyName($tableName, 'fk', ['translation_parent_id']);
        $idxName   = $this->generatePropertyName($tableName, 'idx', ['translation_parent_id']);
        $tableName = "{$this->prefix}$tableName";
        $table     = $schema->getTable($tableName);

        if (!$table->hasColumn('translation_parent_id')) {
            $this->addSql("ALTER TABLE $tableName ADD translation_parent_id INT DEFAULT NULL");
            $this->addSql(
                "ALTER TABLE $tableName ADD CONSTRAINT ".$fkName
                ." FOREIGN KEY (translation_parent_id) REFERENCES $tableName (id) ON DELETE CASCADE"
            );
            $this->addSql("CREATE INDEX $idxName ON $tableName (translation_parent_id)");
        } else {
            // Drop and recreate the parent FK to ensure DELETE CASCADE
            if ($table->hasForeignKey($fkName)) {
                $this->addSql("ALTER TABLE $tableName DROP FOREIGN KEY $fkName");
            }
            $this->addSql(
                "ALTER TABLE $tableName ADD CONSTRAINT ".$fkName
                ." FOREIGN KEY (translation_parent_id) REFERENCES $tableName (id) ON DELETE CASCADE"
            );

            if (!$table->hasIndex($idxName)) {
                $this->addSql("CREATE INDEX $idxName ON $tableName (translation_parent_id)");
            }
        }

        if ($languageColumnName && !$table->hasColumn($languageColumnName)) {
            $this->addSql("ALTER TABLE $tableName ADD COLUMN `lang` varchar(191) NULL");
        }
    }
}
