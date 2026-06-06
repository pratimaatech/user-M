<?php $pageTitle = 'View User'; require __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-person-badge me-2 text-info"></i>User Details
                </h6>
            </div>
            <div class="card-body p-4">

                <!-- Avatar -->
                <div class="text-center mb-4">
                    <div class="rounded-circle bg-primary text-white fw-bold d-inline-flex align-items-center justify-content-center mb-2"
                         style="width:70px;height:70px;font-size:1.5rem">
                        <?= strtoupper(substr($user['name'], 0, 2)) ?>
                    </div>
                    <h5 class="fw-bold mb-0"><?= htmlspecialchars($user['name']) ?></h5>
                    <small class="text-muted">ID #<?= $user['id'] ?></small>
                </div>

                <table class="table table-borderless small">
                    <tr>
                        <td class="text-muted fw-semibold" style="width:120px">Name</td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">Email</td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">Phone</td>
                        <td><?= htmlspecialchars($user['phone']) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">Created</td>
                        <td><?= date('d M Y, h:i A', strtotime($user['created_at'])) ?></td>
                    </tr>
                </table>

                <div class="d-flex gap-2 mt-3">
                    <a href="index.php?action=edit&id=<?= $user['id'] ?>"
                       class="btn btn-warning btn-sm px-3">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    <a href="index.php?action=delete&id=<?= $user['id'] ?>"
                       class="btn btn-danger btn-sm px-3"
                       onclick="return confirm('Is user ko delete karna chahte ho?')">
                        <i class="bi bi-trash me-1"></i>Delete
                    </a>
                    <a href="index.php" class="btn btn-outline-secondary btn-sm ms-auto">
                        <i class="bi bi-arrow-left me-1"></i>Back
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
