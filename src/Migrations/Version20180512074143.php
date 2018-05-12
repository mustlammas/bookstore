<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/*
DROP SCHEMA books cascade;
CREATE SCHEMA books;

GRANT USAGE ON SCHEMA books TO book_manager;

CREATE TABLE books.book (
	isbn		varchar(50) PRIMARY KEY,
	title		varchar(100) NOT NULL,
	added_on	timestamp NOT NULL
);

CREATE TABLE books.label (
	id	SERIAL PRIMARY KEY,
	label	varchar(100)
);

CREATE TABLE books.label_to_book (
	label_id 	integer references books.label(id) NOT NULL,
	isbn		varchar(100) references books.book(isbn) NOT NULL,
	UNIQUE (label_id, isbn)
);

GRANT SELECT, INSERT, UPDATE ON ALL TABLES IN SCHEMA books TO book_manager;

*/

final class Version20180512074143 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $books = $schema->createTable('books');
        $books->addColumn('isbn', 'string', array('length' => 50));
        $books->addColumn('title', 'string', array('length' => 200));
        $books->addColumn('added_on', 'datetime');
        $books->setPrimaryKey(array('isbn'));

        $labels = $schema->createTable('labels');
        $labels->addColumn('id', 'integer', array('autoincrement' => true));
        $labels->addColumn('value', 'string', array('length' => 100));
        $labels->setPrimaryKey(array('id'));

        $bookToLabel = $schema->createTable('book_to_label');
        $bookToLabel->addColumn('isbn', 'string', array('length' => 50));
        $bookToLabel->addColumn('label_id', 'integer');
        $bookToLabel->addForeignKeyConstraint($books, ['isbn'], ['isbn'], [], 'FK_book_to_label_book');
        $bookToLabel->addForeignKeyConstraint($labels, ['label_id'], ['id'], [], 'FK_book_to_label_label');
        $bookToLabel->addUniqueIndex(['isbn', 'label_id'], 'UQ_IDX_book_to_label');
    }

    public function down(Schema $schema) : void
    {
        $schema->dropTable('book_to_label');
        $schema->dropTable('labels');
        $schema->dropTable('books');
    }
}
