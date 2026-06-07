<?php

namespace App\Controllers;

use App\Models\MatchModel;
use App\Models\VenueModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

class AdminVenues extends BaseController
{
    private MatchModel $matches;
    private VenueModel $venues;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->matches = new MatchModel();
        $this->venues = new VenueModel();
    }

    public function index(): ResponseInterface|string
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        return view('admin/venues/index', [
            'username' => session()->get('admin_username'),
            'venues' => $this->venueListing(),
        ]);
    }

    public function edit(int $venueId): ResponseInterface|string
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $venue = $this->venues->find($venueId);

        if ($venue === null) {
            throw PageNotFoundException::forPageNotFound('Venue not found.');
        }

        return view('admin/venues/edit', [
            'username' => session()->get('admin_username'),
            'venue' => $venue,
            'matchCount' => $this->countMatchesForVenue($venueId),
        ]);
    }

    public function store()
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $validationError = $this->validateVenuePayload();

        if ($validationError !== null) {
            return $validationError;
        }

        $this->venues->insert([
            'name' => $this->normalizedVenueName(),
        ]);

        return redirect()->to('/admin/venues')->with('success', 'Venue added successfully.');
    }

    public function update(int $venueId)
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        if ($this->venues->find($venueId) === null) {
            throw PageNotFoundException::forPageNotFound('Venue not found.');
        }

        $validationError = $this->validateVenuePayload($venueId);

        if ($validationError !== null) {
            return $validationError;
        }

        $this->venues->update($venueId, [
            'name' => $this->normalizedVenueName(),
        ]);

        return redirect()->to('/admin/venues')->with('success', 'Venue updated successfully.');
    }

    public function delete(int $venueId)
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        if ($this->venues->find($venueId) === null) {
            throw PageNotFoundException::forPageNotFound('Venue not found.');
        }

        if ($this->countMatchesForVenue($venueId) > 0) {
            return redirect()->to('/admin/venues')->with('error', 'This venue is assigned to one or more matches and cannot be deleted.');
        }

        $this->venues->delete($venueId);

        return redirect()->to('/admin/venues')->with('success', 'Venue deleted successfully.');
    }

    private function validateVenuePayload(?int $venueId = null)
    {
        $rules = [
            'name' => 'required|min_length[2]|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $duplicate = $this->findVenueByName($this->normalizedVenueName());

        if ($duplicate !== null && (int) $duplicate['id'] !== (int) $venueId) {
            return redirect()->back()->withInput()->with('errors', ['name' => 'A venue with this name already exists.']);
        }

        return null;
    }

    private function normalizedVenueName(): string
    {
        return trim((string) $this->request->getPost('name'));
    }

    private function findVenueByName(string $name): ?array
    {
        return $this->venues->builder()
            ->where('LOWER(name)', strtolower($name))
            ->get()
            ->getRowArray();
    }

    private function countMatchesForVenue(int $venueId): int
    {
        return $this->matches->builder()
            ->where('venue_id', $venueId)
            ->countAllResults();
    }

    private function venueListing(): array
    {
        $venues = $this->venues->orderedList();

        foreach ($venues as &$venue) {
            $venue['match_count'] = $this->countMatchesForVenue((int) $venue['id']);
        }
        unset($venue);

        return $venues;
    }
}
