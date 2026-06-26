<?= $this->extend('admin/template/default') ?>

<?= $this->section('admin_nav') ?>teams<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-card">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="h2 mb-2">Team Management</h1>
            <p class="text-secondary mb-0">Create and manage teams with their rosters.</p>
        </div>
        <a href="<?= site_url('/admin/teams/create') ?>" class="btn btn-primary">Add Team</a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <?php if ($teams === []): ?>
        <p class="text-secondary mb-0">No teams added yet.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle admin-table">
                <thead>
                    <tr>
                        <th>Logo</th>
                        <th>Name</th>
                        <th>Members</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($teams as $team): ?>
                        <tr>
                            <td>
                                <?php if ($team['logo']): ?>
                                    <img src="<?= base_url('assets/images/teams/' . esc($team['logo'])) ?>" alt="<?= esc($team['name']) ?>" style="width:40px;height:40px;object-fit:cover;border-radius:4px;">
                                <?php else: ?>
                                    <span class="text-secondary">—</span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($team['name']) ?></td>
                            <td><?= esc((string) $team['member_count']) ?></td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <a href="<?= site_url('/admin/teams/' . $team['id'] . '/edit') ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <form action="<?= site_url('/admin/teams/' . $team['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Delete this team and all its members?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>