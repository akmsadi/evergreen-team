<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class PlayerCareerStatsTest extends CIUnitTestCase
{
    public function testBuildsAggregateCareerStats(): void
    {
        $stats = (new \App\Libraries\PlayerCareerStats())->build(
            [
                'id' => 42,
                'name' => 'Aamir Khan',
                'status' => 'approved',
            ],
            [
                ['runs' => 40, 'balls' => 50, 'fours' => 2, 'sixes' => 1, 'dismissal_mode' => 'caught', 'match_id' => 10],
                ['runs' => 20, 'balls' => 35, 'fours' => 1, 'sixes' => 0, 'dismissal_mode' => null, 'match_id' => 11],
                ['runs' => 0, 'balls' => 5, 'fours' => 0, 'sixes' => 0, 'dismissal_mode' => 'bowled', 'match_id' => 10],
            ],
            [
                ['overs' => 3.0, 'runs' => 18, 'wickets' => 2, 'match_id' => 10],
                ['overs' => 2.2, 'runs' => 14, 'wickets' => 1, 'match_id' => 11],
            ],
            [
                ['player_id' => 42, 'match_id' => 10],
                ['player_id' => 42, 'match_id' => 11],
            ],
            [
                ['match_id' => 10],
                ['match_id' => 11],
            ],
            [],
            [],
            [
                ['overs' => 3.0, 'runs' => 18, 'wickets' => 2, 'match_id' => 10],
                ['overs' => 2.2, 'runs' => 14, 'wickets' => 1, 'match_id' => 11],
            ],
        );

        $this->assertSame(42, $stats['player']['id']);
        $this->assertSame(2, $stats['matches_played']);
        $this->assertSame(3, $stats['batting']['innings']);
        $this->assertSame(60, $stats['batting']['runs']);
        $this->assertSame(30.0, $stats['batting']['average']);
        $this->assertSame(3, $stats['bowling']['wickets']);
        $this->assertSame(32, $stats['bowling']['runs']);
        $this->assertSame('5.2', $stats['bowling']['overs']);
        $this->assertSame(6.0, $stats['bowling']['economy']);
        $this->assertSame('2/18', $stats['bowling']['best_bowling']);
        $this->assertSame([], $stats['match_rows']);
        $this->assertCount(3, $stats['batting_rows']);
        $this->assertCount(2, $stats['bowling_rows']);
    }
}
