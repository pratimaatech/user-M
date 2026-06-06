<?php $pageTitle = 'Dashboard'; require __DIR__ . '/../layout/header.php'; ?>

<!-- Stats card -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-primary bg-opacity-10 p-3">
                    <i class="bi bi-people-fill text-primary fs-4"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bold"><?= $totalUsers ?></div>
                    <div class="text-muted small">Total Users</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Users table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-table me-2 text-primary"></i>All Users</h6>
        <a href="index.php?action=create" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Add User
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Created</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            Koi user nahi mila. <a href="index.php?action=create">Pehla user add karo.</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $i => $u): ?>
                    <tr>
                        <td class="text-muted small"><?= $i + 1 ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center"
                                     style="width:34px;height:34px;font-size:.75rem">
                                    <?= strtoupper(substr($u['name_to_use'], 0, 2)) ?>
                                </div>
                                <span class="fw-semibold small"><?= htmlspecialchars($u['name_to_use']) ?></span>
                            </div>
                        </td>
                        <td class="small"><?= htmlspecialchars($u['email']) ?></td>
                        <td class="small"><?= htmlspecialchars($u['phone']) ?></td>
                        <td class="small text-muted">
                            <?= date('d M Y', strtotime($u['created_at'])) ?>
                        </td>
                        <td class="text-center">
                            <a href="index.php?action=view&id=<?= $u['id'] ?>"
                               class="btn btn-sm btn-outline-info py-0 px-2" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="index.php?action=edit&id=<?= $u['id'] ?>"
                               class="btn btn-sm btn-outline-warning py-0 px-2" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="index.php?action=delete&id=<?= $u['id'] ?>"
                               class="btn btn-sm btn-outline-danger py-0 px-2" title="Delete"
                               onclick="return confirm('<?= htmlspecialchars($u['name_to_use']) ?> ko delete karna chahte ho?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
