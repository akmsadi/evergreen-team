<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVenueReferencesToMatches extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('name', false, true);
        $this->forge->createTable('venues');

        $this->seedVenuesFromMatches();

        $this->forge->addColumn('matches', [
            'venue_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'venue',
            ],
        ]);

        // Update venue_id based on venue name
        $this->db->query('UPDATE matches INNER JOIN venues ON TRIM(matches.venue) = venues.name SET matches.venue_id = venues.id WHERE matches.venue IS NOT NULL AND TRIM(matches.venue) != ""');

        // Add index and foreign key constraint
        $this->db->query('ALTER TABLE matches ADD INDEX matches_venue_id_index (venue_id)');
        $this->db->query('ALTER TABLE matches ADD CONSTRAINT matches_venue_id_foreign FOREIGN KEY (venue_id) REFERENCES venues(id) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE matches DROP FOREIGN KEY matches_venue_id_foreign');
        $this->db->query('ALTER TABLE matches DROP INDEX matches_venue_id_index');
        $this->forge->dropColumn('matches', 'venue_id');
        $this->forge->dropTable('venues', true);
    }

    private function seedVenuesFromMatches(): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $defaultVenues = [
            'Mirpur Cricket Ground, Dhaka',
            'Dhanmondi Club Field, Dhaka',
        ];
        $existingVenues = $this->db->table('matches')
            ->select('TRIM(venue) AS name')
            ->where('venue IS NOT NULL')
            ->where('TRIM(venue) !=', '')
            ->groupBy('TRIM(venue)')
            ->get()
            ->getResultArray();

        $venueNames = array_values(array_unique(array_filter(array_merge(
            $defaultVenues,
            array_map(static fn(array $row): string => (string) $row['name'], $existingVenues)
        ))));

        if ($venueNames === []) {
            return;
        }

        $rows = array_map(static fn(string $name): array => [
            'name' => $name,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ], $venueNames);

        $this->db->table('venues')->insertBatch($rows);
    }
}
