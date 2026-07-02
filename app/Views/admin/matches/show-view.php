<?= $this->extend('admin/template/default') ?>

<?= $this->section('content') ?>
<div class="content-card">
    <div class="panel">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-6">
            <div>
                <h1 class="h2 mb-2"><?= esc($match['team_name']) ?> vs <?= esc($match['opponent_name']) ?></h1>
                <p class="text-secondary mb-0">Use the dedicated start screen for innings and live scoring, then reconcile match expenses and player balances here.</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= site_url('/admin/matches/' . $match['id'] . '/edit') ?>" class="btn btn-primary">Edit Match</a>
                <a href="<?= site_url('/admin/matches/' . $match['id'] . '/scoreboard') ?>" class="btn btn-primary">Edit Scoreboard</a>
                <form action="<?= site_url('/admin/matches/' . $match['id'] . '/clear-scoreboard') ?>" method="post" onsubmit="return confirm('Clear the match scoreboard? All innings and ball data will be deleted. You can then start a new scoreboard.');" class="d-inline d-none">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline-warning">Clear Scoreboard</button>
                </form>
                <?php if (($match['match_status'] ?? '') === 'scheduled'): ?>
                    <form action="<?= site_url('/admin/matches/' . $match['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Archive this match? It will be hidden from the active list but not permanently removed.');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline-danger">Archive Match</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <?php $expenseErrors = session()->getFlashdata('expense_errors') ?? []; ?>
        <?php $expenseUpdateErrors = session()->getFlashdata('expense_update_errors') ?? []; ?>
        <?php $expenseEditId = (int) (session()->getFlashdata('expense_edit_id') ?? 0); ?>

        <?= view('admin/matches/_show_body', get_defined_vars()) ?>
    </div>
</div>
<?= $this->endSection() ?>