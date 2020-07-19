<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\Finder\Finder;

final class Version20200719142329 extends AbstractMigration
{

    public function getDescription() : string
    {
        return 'Initial Migration';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE joke (id INTEGER PRIMARY KEY, content VARCHAR(255) NOT NULL);');
        $this->seedData();
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE joke');
    }

    private function seedData() : void
    {
        $this->addSql($this->seedInsertStatement());
    }

    private function seedInsertStatement() : string
    {
        $values = implode(",", $this->prepareDataForStatement());
        return "INSERT INTO joke (id, content) VALUES {$values}";
    }

    private function prepareDataForStatement() : array
    {
        return array_map(function($row){
          return "(".$row.")";
        }, $this->parseCSVData());
    }

    private function parseCSVData() : array
    {
        $finder = new Finder;
        $finder->in('/srv/app/var')->files()->name('reddit-cleanjokes.csv');
        $rows = [];

        foreach ($finder as $file) { $csv = $file; }

        if (($handle = fopen($csv->getRealPath(), "r")) !== FALSE) {
            $i = 0;
            while (($data = fgetcsv($handle, null, PHP_EOL)) !== FALSE) {
                $i++;
                if ($i == 1) { continue; }
                $rows[] = $data[0];
            }
            fclose($handle);
        }
        return $rows;
    }
}
