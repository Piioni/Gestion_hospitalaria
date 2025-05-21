<?php
$pageTitle = "Listado de Usuarios";
$success = $_GET['success'] ?? null;
?>

<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-md-8">
            <h1>Usuarios</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="/users/create" class="btn btn-primary">Crear Usuario</a>
        </div>
    </div>
    
    <?php if ($success === 'created'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Usuario creado exitosamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-body">
            <?php if (empty($users)): ?>
                <p class="text-muted">No hay usuarios registrados.</p>
            <?php else: ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user->getId()) ?></td>
                                <td><?= htmlspecialchars($user->getName()) ?></td>
                                <td><?= htmlspecialchars($user->getEmail()) ?></td>
                                <td>
                                    <a href="/users/show?id=<?= $user->getId() ?>" class="btn btn-sm btn-info">Ver</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
