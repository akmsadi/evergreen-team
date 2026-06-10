<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class BackupDatabase extends BaseCommand
{
    protected $group       = 'App';
    protected $name        = 'task:backup-database';
    protected $description = 'Exports the MySQL database via mysqldump and uploads it to Dropbox.';

    private string $dropboxClientId     = 'cnhkizup2jsz2r3';
    private string $dropboxClientSecret = '8loj5xepfbbdekz';
    private string $dropboxRefreshToken = 'K0h1KvLhr2YAAAAAAAAAAS1Pr6FvQAKgFwgqBMIXiBNnwXnxj3oTffLoxX87Sjuq';
    // /usr/local/bin/ea-php81 /home/myperson/public_html/evergreen/spark task:backup-database


    public function run(array $params)
    {
        $dbConfig = config('Database')->default;
        $host     = $dbConfig['hostname'];
        $port     = $dbConfig['port'] ?? 3306;
        $user     = $dbConfig['username'];
        $pass     = $dbConfig['password'];
        $name     = $dbConfig['database'];

        $dumpFile = WRITEPATH . 'database/' . date('Y-m-d_H-i-s') . '.sql';
        if (!is_dir(WRITEPATH . 'database')) {
            mkdir(WRITEPATH . 'database', 0755, true);
        }

        CLI::write('Running mysqldump...');

        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s --password=%s %s > %s 2>&1',
            escapeshellarg($host),
            escapeshellarg((string) $port),
            escapeshellarg($user),
            escapeshellarg($pass),
            escapeshellarg($name),
            escapeshellarg($dumpFile)
        );

        exec($command, $output, $exitCode);

        if ($exitCode !== 0 || !is_file($dumpFile) || filesize($dumpFile) === 0) {
            CLI::error('mysqldump failed: ' . implode("\n", $output));
            return EXIT_ERROR;
        }

        CLI::write('Obtaining Dropbox access token...');
        $tokenResponse = $this->getDropboxAccessToken();

        if (empty($tokenResponse['access_token'])) {
            unlink($dumpFile);
            CLI::error('Failed to obtain Dropbox access token.');
            return EXIT_ERROR;
        }

        $accessToken = $tokenResponse['access_token'];
        $dropboxPath = '/evergreen_database/' . basename($dumpFile);

        CLI::write('Uploading to Dropbox: ' . $dropboxPath);

        $ch = curl_init('https://content.dropboxapi.com/2/files/upload');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/octet-stream',
                'Dropbox-API-Arg: ' . json_encode([
                    'path'       => $dropboxPath,
                    'mode'       => 'overwrite',
                    'autorename' => false,
                    'mute'       => true,
                ]),
            ],
            CURLOPT_POSTFIELDS     => file_get_contents($dumpFile),
        ]);

        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        unlink($dumpFile);

        if ($curlError) {
            CLI::error('cURL Error: ' . $curlError);
            return EXIT_ERROR;
        }

        $result = json_decode($response, true);
        if ($httpCode !== 200) {
            CLI::error('Backup failed: ' . ($result['error_summary'] ?? 'Unknown error'));
            return EXIT_ERROR;
        }

        CLI::write('Backup successful! Uploaded as ' . $dropboxPath, 'green');
        return EXIT_SUCCESS;
    }

    private function getDropboxAccessToken(): array
    {
        $ch = curl_init('https://api.dropboxapi.com/oauth2/token');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'grant_type'    => 'refresh_token',
                'refresh_token' => $this->dropboxRefreshToken,
                'client_id'     => $this->dropboxClientId,
                'client_secret' => $this->dropboxClientSecret,
            ]),
        ]);
        $response = curl_exec($ch);
        return json_decode($response, true) ?: [];
    }
}
