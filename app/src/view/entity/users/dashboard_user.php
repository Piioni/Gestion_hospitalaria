<?php

use model\service\RoleService;
use model\service\UserService;

$success = $_GET['success'] ?? null;

$userService = new UserService();
$roleService = new RoleService();

// Obtener la lista de usuarios
$users = $userService->getAllUsers();
if (empty($users)) {
    $users = [];
}
// Inicializar variables y mensajes
$errors = [];
$input = [];

$pageTitle = "Listado de Usuarios";
include __DIR__ . '/../../layouts/_header.php'; ?>

<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="page-title mb-3">Usuarios</h1>
            <p class="lead-text mb-0">Gestión de usuarios del sistema</p>
        </div>
        <div class="col-md-4 text-end d-flex justify-content-end align-items-center">
            <a href="/users/create" class="btn btn-primary btn-lg">
                <i class="bi bi-person-plus me-2"></i> Crear Usuario
            </a>
        </div>
    </div>

    <?php if ($success === 'created'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Usuario creado exitosamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h3 class="mb-0">Listado de Usuarios</h3>
        </div>
        <div class="card-body">
            <?php if (empty($users)): ?>
                <div class="empty-state">
                    <i class="bi bi-people" style="font-size: 3rem; color: var(--muted-text);"></i>
                    <p class="mt-3 text-muted">No hay usuarios registrados.</p>
                    <a href="/users/create" class="btn btn-primary mt-2">Crear el primer usuario</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user->getId()) ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person-circle me-2 text-primary"></i>
                                        <span><?= htmlspecialchars($user->getNombre()) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <a href="mailto:<?= htmlspecialchars($user->getEmail()) ?>"
                                       class="text-decoration-none">
                                        <?= htmlspecialchars($user->getEmail()) ?>
                                    </a>
                                </td>
                                <td>
                                    <?php
                                    $role = $roleService->getRoleById($user->getRol());
                                    if ($role): ?>
                                        <span class="badge bg-primary"><?= htmlspecialchars($role->getNombre()) ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Sin rol asignado</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="actions-column justify-content-center gap-3">
                                        <a href="/users/locations?user_id=<?= $user->getId() ?>"
                                           class="btn btn-sm btn-info text-white" title="Asignar ubicaciones">
                                            <i class="bi bi-geo-alt me-1"></i> Ubicaciones
                                        </a>
                                        <a href="/users/edit?id=<?= $user->getId() ?>"
                                           class="btn btn-sm btn-primary" title="Editar usuario">
                                            <i class="bi bi-pencil-square me-1"></i> Editar
                                        </a>
                                        <button type="button" 
                                                data-user-id="<?= $user->getId() ?>"
                                                class="btn btn-sm btn-danger delete-user" title="Eliminar usuario">
                                            <i class="bi bi-trash-fill me-1"></i> Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/_footer.php'; ?>
