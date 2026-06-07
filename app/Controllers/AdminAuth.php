<?php

namespace App\Controllers;

use App\Libraries\MatchFinanceService;
use App\Models\AdminModel;
use App\Models\MatchModel;
use App\Models\PlayerModel;
use App\Models\VenueModel;
use CodeIgniter\HTTP\ResponseInterface;

class AdminAuth extends BaseController
{
    private AdminModel $admins;
    private MatchFinanceService $matchFinance;
    private MatchModel $matches;
    private PlayerModel $players;

    protected $dropbox_client_id = 'cnhkizup2jsz2r3';
    protected $dropbox_client_secret = '8loj5xepfbbdekz';
    protected $dropbox_refresh_token = 'K0h1KvLhr2YAAAAAAAAAAS1Pr6FvQAKgFwgqBMIXiBNnwXnxj3oTffLoxX87Sjuq';

    public function __construct()
    {
        helper(['form', 'url']);
        $this->admins = new AdminModel();
        $this->matchFinance = new MatchFinanceService();
        $this->matches = new MatchModel();
        $this->players = new PlayerModel();
        $this->venues = new VenueModel();
    }

    public function login(): ResponseInterface|string
    {
        if (session()->get('admin_id') !== null) {
            return redirect()->to('/admin/dashboard');
        }

        return view('admin/login', [
            'validation' => service('validation'),
        ]);
    }

    public function attemptLogin()
    {
        $rules = [
            'login'    => 'required',
            'password' => 'required|min_length[6]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $login = (string) $this->request->getPost('login');
        $password = (string) $this->request->getPost('password');

        $admin = $this->admins
            ->groupStart()
            ->where('username', $login)
            ->orWhere('email', $login)
            ->groupEnd()
            ->first();

        if ($admin === null || (int) $admin['is_active'] !== 1 || ! password_verify($password, $admin['password'])) {
            return redirect()->back()->withInput()->with('auth_error', 'Invalid admin credentials.');
        }

        session()->set([
            'admin_id'       => $admin['id'],
            'admin_username' => $admin['username'],
            'is_admin'       => true,
        ]);

        return redirect()->to('/admin/dashboard');
    }

    public function dashboard(): ResponseInterface|string
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        return view('admin/dashboard', [
            'username' => session()->get('admin_username'),
            'monthlyMatchCounts' => $this->matches->monthlyCounts(),
            'recentMatches' => $this->matches->recentList(10),
            'recentPlayers' => $this->players->recentList(10),
            'topBatsmen' => $this->matchFinance->getTopBatsmen(),
            'topBowlers' => $this->matchFinance->getTopBowlers(),
        ]);
    }

    public function logout()
    {
        session()->remove(['admin_id', 'admin_username', 'is_admin']);

        return redirect()->to('/admin/login')->with('success', 'You have been logged out.');
    }

    public function backupDatabase()
    {
        $filename = 'database.db';
        $filepath = WRITEPATH . 'database/' . $filename;

        if (!is_file($filepath)) {
            return redirect()->to('/admin/dashboard')->with('error', 'Database file not found');
        }

        // Get access token from refresh token
        $tokenResponse = $this->getDropboxAccessToken();

        if (empty($tokenResponse['access_token'])) {
            return redirect()->to('/admin/dashboard')->with('error', 'Failed to obtain Dropbox access token');
        }

        $accessToken = $tokenResponse['access_token'];

        $dropboxPath = '/evergreen_database/' . date('Y-m-d_H-i-s') . '_' . $filename;

        $ch = curl_init('https://content.dropboxapi.com/2/files/upload');

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/octet-stream',
                'Dropbox-API-Arg: ' . json_encode([
                    'path'       => $dropboxPath,
                    'mode'       => 'overwrite',
                    'autorename' => false,
                    'mute'       => true,
                ]),
            ],
            CURLOPT_POSTFIELDS => file_get_contents($filepath),
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        curl_close($ch);

        if ($curlError) {
            return redirect()->to('/admin/dashboard')->with('error', 'cURL Error: ' . $curlError);
        }

        $result = json_decode($response, true);

        if ($httpCode !== 200) {
            return redirect()->to('/admin/dashboard')->with('error', 'Database backup failed! ' . ($result['error_summary'] ?? 'Unknown error'));
        }

        return redirect()->to('/admin/dashboard')->with('success', 'Database backup successful! Uploaded to Dropbox as ' . $dropboxPath);
    }

    private function getDropboxAccessToken(): array
    {
        $ch = curl_init('https://api.dropboxapi.com/oauth2/token');

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'grant_type'    => 'refresh_token',
                'refresh_token' => $this->dropbox_refresh_token,
                'client_id'     => $this->dropbox_client_id,
                'client_secret' => $this->dropbox_client_secret,
            ]),
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true) ?: [];
    }
}
