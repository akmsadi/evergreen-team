<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

use App\Libraries\MatchFinanceService;
use App\Models\MatchExpenseModel;
use App\Models\PlayerModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class AdminExpenses extends BaseController
{
    private MatchExpenseModel $matchExpenses;
    private MatchFinanceService $matchFinance;
    private PlayerModel $players;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        helper(['form', 'url']);
        $this->matchExpenses = new MatchExpenseModel();
        $this->matchFinance  = new MatchFinanceService();
        $this->players       = new PlayerModel();
    }

    public function index(): ResponseInterface|string
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        return view('admin/expenses/index-view', [
            'activeNav'          => 'expenses',
            'expenses'           => $this->matchFinance->getGeneralExpenses(),
            'players'            => $this->players->where('status !=', 'rejected')->orderBy('name', 'ASC')->findAll(),
            'expenseErrors'      => session()->getFlashdata('expense_errors') ?? [],
            'expenseUpdateErrors' => session()->getFlashdata('expense_update_errors') ?? [],
            'expenseEditId'      => (int) (session()->getFlashdata('expense_edit_id') ?? 0),
        ]);
    }

    public function store(): ResponseInterface
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $rules = [
            'expense_title'  => 'required|min_length[2]',
            'expense_amount' => 'required|decimal|greater_than[0]',
            'expense_notes'  => 'permit_empty|string',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/admin/expenses')
                ->withInput()
                ->with('expense_errors', $this->validator->getErrors());
        }

        $contributorIds = array_values(array_unique(array_filter(
            array_map('intval', (array) $this->request->getPost('contributor_ids'))
        )));

        if ($contributorIds === []) {
            return redirect()->to('/admin/expenses')
                ->withInput()
                ->with('expense_errors', ['contributor_ids' => 'Select at least one contributor.']);
        }

        $expenseId = $this->matchExpenses->insert([
            'match_id' => null,
            'title'    => (string) $this->request->getPost('expense_title'),
            'amount'   => $this->normalizeAmount((string) $this->request->getPost('expense_amount')),
            'notes'    => $this->emptyToNull((string) $this->request->getPost('expense_notes')),
        ], true);

        $this->matchFinance->syncExpenseContributors((int) $expenseId, $contributorIds);

        return redirect()->to('/admin/expenses')->with('success', 'Expense added successfully.');
    }

    public function update(int $expenseId): ResponseInterface
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        if ($this->matchFinance->getGeneralExpenseRecord($expenseId) === null) {
            throw PageNotFoundException::forPageNotFound('Expense not found.');
        }

        $rules = [
            'expense_title'  => 'required|min_length[2]',
            'expense_amount' => 'required|decimal|greater_than[0]',
            'expense_notes'  => 'permit_empty|string',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/admin/expenses')
                ->withInput()
                ->with('expense_update_errors', $this->validator->getErrors())
                ->with('expense_edit_id', $expenseId);
        }

        $contributorIds = array_values(array_unique(array_filter(
            array_map('intval', (array) $this->request->getPost('contributor_ids'))
        )));

        if ($contributorIds === []) {
            return redirect()->to('/admin/expenses')
                ->withInput()
                ->with('expense_update_errors', ['contributor_ids' => 'Select at least one contributor.'])
                ->with('expense_edit_id', $expenseId);
        }

        $this->matchExpenses->update($expenseId, [
            'title'  => (string) $this->request->getPost('expense_title'),
            'amount' => $this->normalizeAmount((string) $this->request->getPost('expense_amount')),
            'notes'  => $this->emptyToNull((string) $this->request->getPost('expense_notes')),
        ]);

        $this->matchFinance->syncExpenseContributors($expenseId, $contributorIds);

        return redirect()->to('/admin/expenses')->with('success', 'Expense updated successfully.');
    }

    public function delete(int $expenseId): ResponseInterface
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        if ($this->matchFinance->getGeneralExpenseRecord($expenseId) === null) {
            throw PageNotFoundException::forPageNotFound('Expense not found.');
        }

        $db = db_connect();
        $db->table('match_expense_contributors')->where('expense_id', $expenseId)->delete();
        $this->matchExpenses->delete($expenseId);

        return redirect()->to('/admin/expenses')->with('success', 'Expense deleted successfully.');
    }

    private function emptyToNull(string $value): ?string
    {
        $trimmed = trim($value);
        return $trimmed === '' ? null : $trimmed;
    }

    private function normalizeAmount(string $value): string
    {
        return number_format((float) $value, 2, '.', '');
    }
}
