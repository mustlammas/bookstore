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
