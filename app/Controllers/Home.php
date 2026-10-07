<?php

namespace App\Controllers;

use App\Models\CustomerAccountModel;
use App\Models\UserAccountModel;

class Home extends BaseController
{
    protected CustomerAccountModel $customerModel;

    public function __construct()
    {
        $this->customerModel = new CustomerAccountModel();
    }

    public function index(): string
    {
        $data = [
            'title' => 'PowerFlow Electric - Reliable Energy Solutions',
            'page' => 'home'
        ];
        return view('home', $data);
    }

    public function login()
    {
        if (session()->get('isLogged') === true) return redirect()->to(base_url('dashboard'));
        if ($this->request->getMethod() === 'POST') {
            $username = trim((string) $this->request->getPost('username'));
            $password = (string) $this->request->getPost('password');
            if ($username === '' || $password === '') return redirect()->back()->withInput()->with('error', 'Enter both your username and password.');
            $user = (new UserAccountModel())->where('username', $username)->first();
            $valid = $user && (password_verify($password, $user['password']) || hash_equals((string) $user['password'], $password));
            if (!$valid) return redirect()->back()->withInput()->with('error', 'Invalid username or password.');
            session()->regenerate();
            session()->set(['isLogged' => true, 'user_id' => $user['id'], 'username' => $user['username']]);
            return redirect()->to(base_url('dashboard'));
        }
        return view('login');
    }

    public function dashboard()
    {
        if (!$this->requireLogin()) return redirect()->to(base_url('login'));
        $keyword = trim((string) $this->request->getGet('search'));
        $status = (string) $this->request->getGet('status');
        $type = (string) $this->request->getGet('type');
        $perPage = 10;
        if ($keyword) $accounts = $this->customerModel->searchAccounts($keyword, $perPage);
        elseif ($status) $accounts = $this->customerModel->getAccountsByStatus($status, $perPage);
        elseif ($type) $accounts = $this->customerModel->getAccountsByType($type, $perPage);
        else $accounts = $this->customerModel->getAccountsPaginated($perPage);
        return view('dashboard_accounts', [
            'accounts' => $accounts, 'pager' => $this->customerModel->pager,
            'total_accounts' => $this->customerModel->getTotalAccounts(),
            'active_accounts' => $this->customerModel->getCountByStatus('active'),
            'inactive_accounts' => $this->customerModel->getCountByStatus('inactive'),
            'suspended_accounts' => $this->customerModel->getCountByStatus('suspended'),
            'current_page' => $this->request->getGet('page') ?? 1,
            'search_keyword' => $keyword, 'filter_status' => $status, 'filter_type' => $type,
            'username' => session()->get('username'), 'flash' => session()->getFlashdata('message')
        ]);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('login'))->with('success', 'You have been logged out.');
    }

    public function viewAccount($id)
    {
        if (!$this->requireLogin()) return redirect()->to(base_url('login'));
        $account = $this->customerModel->find($id);
        if (!$account) return redirect()->to(base_url('dashboard'))->with('message', 'Account not found.');
        return view('account_view', ['account' => $account, 'username' => session()->get('username')]);
    }

    public function newAccount()
    {
        if (!$this->requireLogin()) return redirect()->to(base_url('login'));
        return view('account_form', ['account' => null, 'mode' => 'create', 'username' => session()->get('username')]);
    }

    public function createAccount()
    {
        if (!$this->requireLogin()) return redirect()->to(base_url('login'));
        $data = $this->accountData();
        if (!$this->validateAccount($data)) return redirect()->back()->withInput()->with('message', implode(' ', $this->validator->getErrors()));
        $this->customerModel->insert($data);
        return redirect()->to(base_url('dashboard'))->with('message', 'Customer account created.');
    }

    public function editAccount($id)
    {
        if (!$this->requireLogin()) return redirect()->to(base_url('login'));
        $account = $this->customerModel->find($id);
        if (!$account) return redirect()->to(base_url('dashboard'))->with('message', 'Account not found.');
        return view('account_form', ['account' => $account, 'mode' => 'edit', 'username' => session()->get('username')]);
    }

    public function updateAccount($id)
    {
        if (!$this->requireLogin()) return redirect()->to(base_url('login'));
        $data = $this->accountData();
        if (!$this->validateAccount($data, $id)) return redirect()->back()->withInput()->with('message', implode(' ', $this->validator->getErrors()));
        $this->customerModel->update($id, $data);
        return redirect()->to(base_url('account/' . $id))->with('message', 'Customer account updated.');
    }

    public function deleteAccount($id)
    {
        if (!$this->requireLogin()) return redirect()->to(base_url('login'));
        $this->customerModel->delete($id);
        return redirect()->to(base_url('dashboard'))->with('message', 'Customer account deleted.');
    }

    protected function requireLogin(): bool { return session()->get('isLogged') === true; }
    protected function accountData(): array { return $this->request->getPost(['account_number','customer_name','address','phone','email','meter_number','connection_type','status']); }
    protected function validateAccount(array $data, ?int $id = null): bool
    {
        $rules = ['account_number' => 'required|max_length[50]', 'customer_name' => 'required|max_length[150]', 'address' => 'required', 'phone' => 'permit_empty|max_length[20]', 'email' => 'permit_empty|valid_email', 'meter_number' => 'permit_empty|max_length[50]', 'connection_type' => 'required|in_list[residential,commercial,industrial]', 'status' => 'required|in_list[active,inactive,suspended]'];
        return $this->validate($rules);
    }
}
