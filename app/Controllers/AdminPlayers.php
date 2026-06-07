<?php

namespace App\Controllers;

use App\Libraries\MatchFinanceService;
use App\Models\MatchPlayerDepositModel;
use App\Models\PlayerModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

class AdminPlayers extends BaseController
{
    private MatchFinanceService $matchFinance;
    private MatchPlayerDepositModel $matchPlayerDeposits;
    private PlayerModel $players;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->matchFinance = new MatchFinanceService();
        $this->matchPlayerDeposits = new MatchPlayerDepositModel();
        $this->players = new PlayerModel();
    }

    public function index(): ResponseInterface|string
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        return view('admin/players/index', $this->buildPlayersOverviewData());
    }

    public function accounts(): ResponseInterface|string
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        return view('admin/players/accounts', $this->buildPlayersOverviewData());
    }

    public function create(): ResponseInterface|string
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        return view('admin/players/create', [
            'username' => session()->get('admin_username'),
            'approvedPlayers' => $this->approvedPlayers(),
        ]);
    }

    public function edit(int $playerId): ResponseInterface|string
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $player = $this->players->find($playerId);

        if ($player === null) {
            throw PageNotFoundException::forPageNotFound('Player not found.');
        }

        $playerOverview = $this->matchFinance->buildPlayerOverview([$player]);
        $wallet = $playerOverview[0] ?? [
            'match_count' => 0,
            'deposited' => 0.0,
            'owed' => 0.0,
            'balance' => 0.0,
            'status' => 'settled',
        ];
        $matchBreakdown = $this->matchFinance->getPlayerMatchBreakdown($playerId);

        return view('admin/players/edit', [
            'username' => session()->get('admin_username'),
            'player' => $player,
            'approvedPlayers' => $this->approvedPlayers($playerId),
            'wallet' => $wallet,
            'deposits' => $this->matchFinance->getPlayerDeposits($playerId),
            'matchBreakdown' => $matchBreakdown,
            'matchContributionTotal' => round(array_sum(array_map(static fn(array $match): float => (float) ($match['contribution'] ?? 0), $matchBreakdown)), 2),
        ]);
    }

    public function store()
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        if (! $this->validate($this->playerRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $guestValidationError = $this->validateGuestPlayerSelection();

        if ($guestValidationError !== null) {
            return $guestValidationError;
        }

        $this->players->insert($this->playerPayload());

        return redirect()->to('/admin/players/create')->with('success', 'Player added successfully.');
    }

    public function update(int $playerId)
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        if ($this->players->find($playerId) === null) {
            throw PageNotFoundException::forPageNotFound('Player not found.');
        }

        if (! $this->validate($this->playerRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $guestValidationError = $this->validateGuestPlayerSelection($playerId);

        if ($guestValidationError !== null) {
            return $guestValidationError;
        }

        $this->players->update($playerId, $this->playerPayload());

        return redirect()->to('/admin/players/' . $playerId . '/edit')->with('success', 'Player updated successfully.');
    }

    public function delete(int $playerId)
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        if ($this->players->find($playerId) === null) {
            throw PageNotFoundException::forPageNotFound('Player not found.');
        }

        $this->players->delete($playerId);

        return redirect()->to('/admin/players')->with('success', 'Player deleted successfully.');
    }

    public function storeDeposit()
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $validationError = $this->validateDepositPayload('create');

        if ($validationError !== null) {
            return $validationError;
        }

        $this->matchPlayerDeposits->insert([
            'match_id' => null,
            'player_id' => (int) $this->request->getPost('deposit_player_id'),
            'amount' => $this->normalizeAmount((string) $this->request->getPost('deposit_amount')),
            'notes' => $this->emptyToNull((string) $this->request->getPost('deposit_notes')),
        ]);

        return redirect()->to('/admin/accounts')->with('success', 'Player deposit added successfully.');
    }

    public function updateDeposit(int $depositId)
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        if ($this->matchPlayerDeposits->find($depositId) === null) {
            throw PageNotFoundException::forPageNotFound('Deposit not found.');
        }

        $validationError = $this->validateDepositPayload('edit', $depositId);

        if ($validationError !== null) {
            return $validationError;
        }

        $this->matchPlayerDeposits->update($depositId, [
            'match_id' => null,
            'player_id' => (int) $this->request->getPost('deposit_player_id'),
            'amount' => $this->normalizeAmount((string) $this->request->getPost('deposit_amount')),
            'notes' => $this->emptyToNull((string) $this->request->getPost('deposit_notes')),
        ]);

        return redirect()->to('/admin/accounts')->with('success', 'Player deposit updated successfully.');
    }

    public function deleteDeposit(int $depositId)
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        if ($this->matchPlayerDeposits->find($depositId) === null) {
            throw PageNotFoundException::forPageNotFound('Deposit not found.');
        }

        $this->matchPlayerDeposits->delete($depositId);

        return redirect()->to('/admin/accounts')->with('success', 'Player deposit deleted successfully.');
    }

    private function validateDepositPayload(string $mode, ?int $depositId = null)
    {
        $rules = [
            'deposit_player_id' => 'required|integer|greater_than[0]',
            'deposit_amount' => 'required|decimal|greater_than[0]',
            'deposit_notes' => 'permit_empty|string',
        ];

        if (! $this->validate($rules)) {
            $errorKey = $mode === 'edit' ? 'deposit_update_errors' : 'deposit_errors';

            $redirect = redirect()->to('/admin/accounts')
                ->withInput()
                ->with($errorKey, $this->validator->getErrors())
                ->with('deposit_modal', $mode);

            if ($depositId !== null) {
                $redirect = $redirect->with('deposit_edit_id', $depositId);
            }

            return $redirect;
        }

        $selectedPlayer = $this->players->find((int) $this->request->getPost('deposit_player_id'));

        if ($selectedPlayer === null) {
            $errorKey = $mode === 'edit' ? 'deposit_update_errors' : 'deposit_errors';
            $errors = ['deposit_player_id' => 'Select a valid player.'];

            $redirect = redirect()->to('/admin/accounts')
                ->withInput()
                ->with($errorKey, $errors)
                ->with('deposit_modal', $mode);

            if ($depositId !== null) {
                $redirect = $redirect->with('deposit_edit_id', $depositId);
            }

            return $redirect;
        }

        if ($mode === 'create' && ($selectedPlayer['status'] ?? '') !== 'approved') {
            $errorKey = 'deposit_errors';
            $errors = ['deposit_player_id' => 'Deposits can only be added for approved players.'];

            return redirect()->to('/admin/accounts')
                ->withInput()
                ->with($errorKey, $errors)
                ->with('deposit_modal', $mode);
        }

        return null;
    }

    private function emptyToNull(string $value): ?string
    {
        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }

    private function playerRules(): array
    {
        return [
            'name' => 'required|min_length[2]',
            'email' => 'permit_empty|valid_email',
            'phone' => 'required|min_length[7]',
            'address' => 'required|min_length[5]',
            'status' => 'required|in_list[pending,approved,rejected,guest]',
        ];
    }

    private function playerPayload(): array
    {
        $status = (string) $this->request->getPost('status');

        return [
            'name' => (string) $this->request->getPost('name'),
            'email' => (string) $this->request->getPost('email'),
            'phone' => (string) $this->request->getPost('phone'),
            'address' => (string) $this->request->getPost('address'),
            'status' => $status,
            'guest_of_player_id' => $status === 'guest' ? (int) $this->request->getPost('guest_of_player_id') : null,
        ];
    }

    private function validateGuestPlayerSelection(?int $playerId = null)
    {
        $status = (string) $this->request->getPost('status');

        if ($status !== 'guest') {
            return null;
        }

        $guestOfPlayerId = (int) ($this->request->getPost('guest_of_player_id') ?? 0);

        if ($guestOfPlayerId <= 0) {
            return redirect()->back()->withInput()->with('errors', [
                'guest_of_player_id' => 'Select the approved player this guest belongs to.',
            ]);
        }

        if ($playerId !== null && $guestOfPlayerId === $playerId) {
            return redirect()->back()->withInput()->with('errors', [
                'guest_of_player_id' => 'A guest player cannot be linked to themselves.',
            ]);
        }

        $approvedPlayer = $this->players->builder()
            ->select('id')
            ->where('id', $guestOfPlayerId)
            ->where('status', 'approved')
            ->get()
            ->getRowArray();

        if ($approvedPlayer === null) {
            return redirect()->back()->withInput()->with('errors', [
                'guest_of_player_id' => 'Select a valid approved player.',
            ]);
        }

        return null;
    }

    private function approvedPlayers(?int $excludePlayerId = null): array
    {
        $builder = $this->players->builder()
            ->select('id, name')
            ->where('status', 'approved');

        if ($excludePlayerId !== null) {
            $builder->where('id !=', $excludePlayerId);
        }

        return $builder
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();
    }

    private function buildPlayersOverviewData(): array
    {
        $players = $this->players->orderBy('name', 'ASC')->findAll();

        return [
            'username' => session()->get('admin_username'),
            'players' => $players,
            'approvedPlayers' => $this->approvedPlayers(),
            'playerOverview' => $this->matchFinance->buildPlayerOverview($players),
            'deposits' => $this->matchFinance->getAllDeposits(),
            'matchExpenseSummary' => $this->matchFinance->getMatchExpenseSummary(),
            'playerCount' => count($players),
            'approvedCount' => count(array_filter($players, static fn(array $player): bool => ($player['status'] ?? '') === 'approved')),
            'pendingCount' => count(array_filter($players, static fn(array $player): bool => ($player['status'] ?? '') === 'pending')),
            'rejectedCount' => count(array_filter($players, static fn(array $player): bool => ($player['status'] ?? '') === 'rejected')),
        ];
    }

    private function normalizeAmount(string $value): string
    {
        return number_format((float) $value, 2, '.', '');
    }
}
