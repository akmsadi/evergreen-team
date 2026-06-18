<?php

namespace App\Controllers;

use App\Models\EventModel;
use App\Models\EventImageModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

class AdminEvents extends BaseController
{
    private EventModel $events;
    private EventImageModel $eventImages;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->events      = new EventModel();
        $this->eventImages = new EventImageModel();
    }

    public function index(): ResponseInterface|string
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        return view('admin/events/index', [
            'username' => session()->get('admin_username'),
            'events'   => $this->events->orderedList(),
        ]);
    }

    public function edit(int $eventId): ResponseInterface|string
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $event = $this->events->find($eventId);

        if ($event === null) {
            throw PageNotFoundException::forPageNotFound('Event not found.');
        }

        return view('admin/events/edit', [
            'username' => session()->get('admin_username'),
            'event'    => $event,
            'images'   => $this->eventImages->forEvent($eventId),
        ]);
    }

    public function store(): ResponseInterface
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        if (! $this->validate([
            'title'       => 'required|min_length[2]|max_length[255]',
            'description' => 'permit_empty|max_length[5000]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->events->insert([
            'title'       => trim($this->request->getPost('title')),
            'description' => trim($this->request->getPost('description') ?? ''),
        ]);

        return redirect()->to('/admin/events')->with('success', 'Event created successfully.');
    }

    public function update(int $eventId): ResponseInterface
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        if ($this->events->find($eventId) === null) {
            throw PageNotFoundException::forPageNotFound('Event not found.');
        }

        if (! $this->validate([
            'title'       => 'required|min_length[2]|max_length[255]',
            'description' => 'permit_empty|max_length[5000]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->events->update($eventId, [
            'title'       => trim($this->request->getPost('title')),
            'description' => trim($this->request->getPost('description') ?? ''),
        ]);

        return redirect()->to('/admin/events/' . $eventId . '/edit')->with('success', 'Event updated successfully.');
    }

    public function delete(int $eventId): ResponseInterface
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        if ($this->events->find($eventId) === null) {
            throw PageNotFoundException::forPageNotFound('Event not found.');
        }

        $images = $this->eventImages->forEvent($eventId);

        foreach ($images as $image) {
            $this->removeImageFile($image['image_path']);
        }

        $this->events->delete($eventId);

        return redirect()->to('/admin/events')->with('success', 'Event deleted successfully.');
    }

    public function storeImage(int $eventId): ResponseInterface
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $event = $this->events->find($eventId);

        if ($event === null) {
            throw PageNotFoundException::forPageNotFound('Event not found.');
        }

        $files = $this->request->getFileMultiple('images');

        if (empty($files) || \count($files) === 1 && ! $files[0]->isValid()) {
            return redirect()->back()->with('errors', ['Please select at least one image.']);
        }

        $folderName  = url_title($event['title'], '-', true);
        $uploadPath  = FCPATH . 'assets/images/gallery/' . $folderName;
        $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
        $maxBytes    = 5 * 1024 * 1024;

        if (! is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $errors   = [];
        $uploaded = 0;

        foreach ($files as $file) {
            if (! $file->isValid() || $file->hasMoved()) {
                continue;
            }

            if (! \in_array($file->getMimeType(), $allowedMime, true)) {
                $errors[] = '"' . $file->getClientName() . '" is not a supported image type (JPEG, PNG, WebP only).';
                continue;
            }

            if ($file->getSize() > $maxBytes) {
                $errors[] = '"' . $file->getClientName() . '" exceeds the 5 MB size limit.';
                continue;
            }

            $newName = $file->getRandomName();

            if (! $file->move($uploadPath, $newName)) {
                $errors[] = 'Failed to upload "' . $file->getClientName() . '".';
                continue;
            }

            $this->eventImages->insert([
                'event_id'   => $eventId,
                'image_path' => 'assets/images/gallery/' . $folderName . '/' . $newName,
                'caption'    => '',
            ]);

            $uploaded++;
        }

        $redirectUrl = '/admin/events/' . $eventId . '/edit';

        if (! empty($errors)) {
            $response = redirect()->to($redirectUrl)->with('errors', $errors);
            return $uploaded > 0
                ? $response->with('success', $uploaded . ' image(s) uploaded successfully.')
                : $response;
        }

        return redirect()->to($redirectUrl)->with('success', $uploaded . ' image(s) uploaded successfully.');
    }

    public function deleteImage(int $eventId, int $imageId): ResponseInterface
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $image = $this->eventImages->find($imageId);

        if ($image === null || (int) $image['event_id'] !== $eventId) {
            throw PageNotFoundException::forPageNotFound('Image not found.');
        }

        $this->removeImageFile($image['image_path']);
        $this->eventImages->delete($imageId);

        return redirect()->to('/admin/events/' . $eventId . '/edit')->with('success', 'Image removed successfully.');
    }

    private function removeImageFile(string $imagePath): void
    {
        $fullPath = FCPATH . ltrim($imagePath, '/');

        if (is_file($fullPath)) {
            unlink($fullPath);
        }
    }


}
