<?php
$installer = $this;
$installer->startSetup();

/* Category table */
$table = $installer->getConnection()->newTable($installer->getTable('wsnyc_questionsanswers/category'))
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
            'identity' => true,
        ),        'Category ID'
    )->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => false,
        ),        'Category Name'
    )->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
        ), ' Parent Category ID'
    )->addColumn('parent_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => false,
        ),        'Parent Category Name'
    )->addColumn('image', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => true,
        ),        'Category Image'
    )->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
        ),        'Category Position'
    )->addColumn('created_at',
        Varien_Db_Ddl_Table::TYPE_DATETIME,
        null,
        array(
        ),
        'Category Added'
    )->setComment('wsnyc_questionsanswers/category entity table');
$installer->getConnection()->createTable($table);

/* Question table */

$table = $installer->getConnection()->newTable($installer->getTable('wsnyc_questionsanswers/question'))
    ->addColumn('question_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
        'identity' => true,
    ), 'Question ID')
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
    ), 'Question Category')
    ->addColumn('category_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'unsigned' => true,
        'nullable' => false,
    ), 'Question Category Name')
    ->addColumn('question_text', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
    ), 'Question Text')
    ->addColumn('asked_email', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
    ), 'Question Text')
    ->addColumn('asked_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
    ), 'Question Text')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
    ), 'Question Added datetime')
    ->setComment('wsnyc_questionsanswers/question entity table');
$installer->getConnection()->createTable($table);

/**
 * Answer table:
 * answer_id
 * question_id
 * answer_text
 * answer_email
 * answer_name
 * created_at
 */
$table = $installer->getConnection()->newTable($installer->getTable('wsnyc_questionsanswers/answer'))
    ->addColumn('answer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
        'identity' => true,
    ), 'Answer ID')
    ->addColumn('question_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
    ), 'Question ID')
    ->addColumn('answer_text', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
    ), 'Answer Text')
    ->addColumn('answer_email', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
    ), 'Answer Email')
    ->addColumn('answer_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
    ), 'Question Text')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
    ), 'Question Added datetime')
    ->setComment('wsnyc_questionsanswers/answer entity table');
$installer->getConnection()->createTable($table);

/**
 * Question-Product table:
 * questionproduct_id
 * question_id
 * product_id
 * created_at
 */
$table = $installer->getConnection()->newTable($installer->getTable('wsnyc_questionsanswers/questionproduct'))
    ->addColumn('questionproduct_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
        'identity' => true,
    ), 'Question ID')
    ->addColumn('question_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
    ), 'Question ID')
    ->addColumn('product_sku', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'unsigned' => true,
        'nullable' => false,
    ), 'Question Product SKU')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
    ), 'Question Product Added datetime')
    ->setComment('wsnyc_questionsanswers/answer entity table');
$installer->getConnection()->createTable($table);

$installer->endSetup();