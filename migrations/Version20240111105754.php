<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240111105754 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE briefing_logo DROP FOREIGN KEY FK_E4D107847BAE0197');
        $this->addSql('DROP INDEX UNIQ_E4D107847BAE0197 ON briefing_logo');
        $this->addSql('ALTER TABLE briefing_logo CHANGE usario_id usuario_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE briefing_logo ADD CONSTRAINT FK_E4D10784DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E4D10784DB38439E ON briefing_logo (usuario_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE briefing_logo DROP FOREIGN KEY FK_E4D10784DB38439E');
        $this->addSql('DROP INDEX UNIQ_E4D10784DB38439E ON briefing_logo');
        $this->addSql('ALTER TABLE briefing_logo CHANGE usuario_id usario_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE briefing_logo ADD CONSTRAINT FK_E4D107847BAE0197 FOREIGN KEY (usario_id) REFERENCES usuario (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E4D107847BAE0197 ON briefing_logo (usario_id)');
    }
}
