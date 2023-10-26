<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231026090747 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE book_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE book_category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE book_format_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE book_to_book_format_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE review_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE subscriber_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE book (id INT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, authors TEXT NOT NULL, isbn VARCHAR(13) NOT NULL, description TEXT NOT NULL, publication_date DATE NOT NULL, meap BOOLEAN DEFAULT false NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN book.authors IS \'(DC2Type:simple_array)\'');
        $this->addSql('COMMENT ON COLUMN book.publication_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('CREATE TABLE book_to_book_category (book_id INT NOT NULL, book_category_id INT NOT NULL, PRIMARY KEY(book_id, book_category_id))');
        $this->addSql('CREATE INDEX IDX_57511BE216A2B381 ON book_to_book_category (book_id)');
        $this->addSql('CREATE INDEX IDX_57511BE240B1D29E ON book_to_book_category (book_category_id)');
        $this->addSql('CREATE TABLE book_category (id INT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE book_format (id INT NOT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE book_to_book_format (id INT NOT NULL, book_id INT NOT NULL, format_id INT NOT NULL, price NUMERIC(10, 2) NOT NULL, discount_percent INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D02DE22216A2B381 ON book_to_book_format (book_id)');
        $this->addSql('CREATE INDEX IDX_D02DE222D629F605 ON book_to_book_format (format_id)');
        $this->addSql('CREATE TABLE review (id INT NOT NULL, book_id INT NOT NULL, rating INT NOT NULL, content TEXT NOT NULL, author VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_794381C616A2B381 ON review (book_id)');
        $this->addSql('COMMENT ON COLUMN review.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE subscriber (id INT NOT NULL, email VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN subscriber.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE book_to_book_category ADD CONSTRAINT FK_57511BE216A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE book_to_book_category ADD CONSTRAINT FK_57511BE240B1D29E FOREIGN KEY (book_category_id) REFERENCES book_category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE book_to_book_format ADD CONSTRAINT FK_D02DE22216A2B381 FOREIGN KEY (book_id) REFERENCES book (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE book_to_book_format ADD CONSTRAINT FK_D02DE222D629F605 FOREIGN KEY (format_id) REFERENCES book_format (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C616A2B381 FOREIGN KEY (book_id) REFERENCES book (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE book_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE book_category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE book_format_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE book_to_book_format_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE review_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE subscriber_id_seq CASCADE');
        $this->addSql('ALTER TABLE book_to_book_category DROP CONSTRAINT FK_57511BE216A2B381');
        $this->addSql('ALTER TABLE book_to_book_category DROP CONSTRAINT FK_57511BE240B1D29E');
        $this->addSql('ALTER TABLE book_to_book_format DROP CONSTRAINT FK_D02DE22216A2B381');
        $this->addSql('ALTER TABLE book_to_book_format DROP CONSTRAINT FK_D02DE222D629F605');
        $this->addSql('ALTER TABLE review DROP CONSTRAINT FK_794381C616A2B381');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE book_to_book_category');
        $this->addSql('DROP TABLE book_category');
        $this->addSql('DROP TABLE book_format');
        $this->addSql('DROP TABLE book_to_book_format');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE subscriber');
    }
}
