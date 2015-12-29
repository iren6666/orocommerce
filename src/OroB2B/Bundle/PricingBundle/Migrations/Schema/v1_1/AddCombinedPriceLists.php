<?php

namespace OroB2B\Bundle\PricingBundle\Migrations\Schema\v1_1;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class AddCombinedPriceLists implements Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->createOroB2BPriceListCombinedTable($schema);
        $this->createOroB2BPriceProductCombinedTable($schema);
        $this->createOrob2BPlistCurrCombinedTable($schema);
        $this->createOrob2BCmbPriceListToAccTable($schema);
        $this->createOrob2BCmbPriceListToAccGrTable($schema);
        $this->createOrob2BCmbPriceListToWsTable($schema);

        $this->addOroB2BPriceProductCombinedForeignKeys($schema);
        $this->addOrob2BPlistCurrCombinedForeignKeys($schema);
        $this->addOrob2BCmbPriceListToAccGrForeignKeys($schema);
        $this->addOrob2BCmbPriceListToWsForeignKeys($schema);
        $this->addOrob2BCmbPriceListToAccForeignKeys($schema);
    }

    /**
     * Create orob2b_price_list_combined table
     *
     * @param Schema $schema
     */
    protected function createOroB2BPriceListCombinedTable(Schema $schema)
    {
        $table = $schema->createTable('orob2b_price_list_combined');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('is_enabled', 'boolean', []);
        $table->addColumn('created_at', 'datetime', ['comment' => '(DC2Type:datetime)']);
        $table->addColumn('updated_at', 'datetime', ['comment' => '(DC2Type:datetime)']);
        $table->setPrimaryKey(['id']);
    }

    /**
     * Create orob2b_price_product_combined table
     *
     * @param Schema $schema
     */
    protected function createOroB2BPriceProductCombinedTable(Schema $schema)
    {
        $table = $schema->createTable('orob2b_price_product_combined');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('unit_code', 'string', ['length' => 255]);
        $table->addColumn('product_id', 'integer', []);
        $table->addColumn('combined_price_list_id', 'integer', []);
        $table->addColumn('product_sku', 'string', ['length' => 255]);
        $table->addColumn('quantity', 'float', []);
        $table->addColumn('value', 'money', ['precision' => 19, 'scale' => 4, 'comment' => '(DC2Type:money)']);
        $table->addColumn('currency', 'string', ['length' => 3]);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(
            [
                'product_id',
                'combined_price_list_id',
                'quantity',
                'unit_code',
                'currency'
            ],
            'orob2b_combined_price_uidx'
        );
    }

    /**
     * Create orob2b_plist_curr_combined table
     *
     * @param Schema $schema
     */
    protected function createOrob2BPlistCurrCombinedTable(Schema $schema)
    {
        $table = $schema->createTable('orob2b_plist_curr_combined');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('combined_price_list_id', 'integer', []);
        $table->addColumn('currency', 'string', ['length' => 3]);
        $table->setPrimaryKey(['id']);
    }

    /**
     * Create orob2b_cmb_price_list_to_acc table
     *
     * @param Schema $schema
     */
    protected function createOrob2BCmbPriceListToAccTable(Schema $schema)
    {
        $table = $schema->createTable('orob2b_cmb_price_list_to_acc');
        $table->addColumn('account_id', 'integer', []);
        $table->addColumn('combined_price_list_id', 'integer', []);
        $table->addColumn('website_id', 'integer', []);
        $table->setPrimaryKey(['account_id', 'combined_price_list_id', 'website_id']);
    }

    /**
     * Create orob2b_cmb_plist_to_acc_gr table
     *
     * @param Schema $schema
     */
    protected function createOrob2BCmbPriceListToAccGrTable(Schema $schema)
    {
        $table = $schema->createTable('orob2b_cmb_plist_to_acc_gr');
        $table->addColumn('account_group_id', 'integer', []);
        $table->addColumn('combined_price_list_id', 'integer', []);
        $table->addColumn('website_id', 'integer', []);
        $table->setPrimaryKey(['account_group_id', 'combined_price_list_id', 'website_id']);
    }

    /**
     * Create orob2b_cmb_price_list_to_ws table
     *
     * @param Schema $schema
     */
    protected function createOrob2BCmbPriceListToWsTable(Schema $schema)
    {
        $table = $schema->createTable('orob2b_cmb_price_list_to_ws');
        $table->addColumn('combined_price_list_id', 'integer', []);
        $table->addColumn('website_id', 'integer', []);
        $table->setPrimaryKey(['combined_price_list_id', 'website_id']);
    }

    /**
     * Add orob2b_price_product_combined foreign keys.
     *
     * @param Schema $schema
     */
    protected function addOroB2BPriceProductCombinedForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('orob2b_price_product_combined');
        $table->addForeignKeyConstraint(
            $schema->getTable('orob2b_product_unit'),
            ['unit_code'],
            ['code'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('orob2b_product'),
            ['product_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('orob2b_price_list_combined'),
            ['combined_price_list_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
    }

    /**
     * Add orob2b_plist_curr_combined foreign keys.
     *
     * @param Schema $schema
     */
    protected function addOrob2BPlistCurrCombinedForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('orob2b_plist_curr_combined');
        $table->addForeignKeyConstraint(
            $schema->getTable('orob2b_price_list_combined'),
            ['combined_price_list_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
    }

    /**
     * Add orob2b_cmb_plist_to_acc_gr foreign keys.
     *
     * @param Schema $schema
     */
    protected function addOrob2BCmbPriceListToAccGrForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('orob2b_cmb_plist_to_acc_gr');
        $table->addForeignKeyConstraint(
            $schema->getTable('orob2b_website'),
            ['website_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('orob2b_price_list_combined'),
            ['combined_price_list_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('orob2b_account_group'),
            ['account_group_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
    }

    /**
     * Add orob2b_cmb_price_list_to_ws foreign keys.
     *
     * @param Schema $schema
     */
    protected function addOrob2BCmbPriceListToWsForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('orob2b_cmb_price_list_to_ws');
        $table->addForeignKeyConstraint(
            $schema->getTable('orob2b_website'),
            ['website_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('orob2b_price_list_combined'),
            ['combined_price_list_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
    }

    /**
     * Add orob2b_cmb_price_list_to_acc foreign keys.
     *
     * @param Schema $schema
     */
    protected function addOrob2BCmbPriceListToAccForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('orob2b_cmb_price_list_to_acc');
        $table->addForeignKeyConstraint(
            $schema->getTable('orob2b_website'),
            ['website_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('orob2b_price_list_combined'),
            ['combined_price_list_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('orob2b_account'),
            ['account_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
    }
}
