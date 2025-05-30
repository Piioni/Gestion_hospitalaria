<?php

namespace controllers;

use Exception;
use middleware\AuthMiddleware;
use model\entity\User;
use model\service\AuthService;
use model\service\BotiquinService;
use model\service\HospitalService;
use model\service\PlantaService;
use model\service\RoleService;
use model\service\UserService;
use model\repository\UserLocationRepository;

class UserController extends BaseController
{
    private UserService $userService;
    private RoleService $roleService;
    private HospitalService $hospitalService;
    private PlantaService $plantaService;
    private BotiquinService $botiquinService;
    private UserLocationRepository $userLocationRepository;
    private AuthService $authService;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->roleService = new RoleService();
        $this->hospitalService = new HospitalService();
        $this->plantaService = new PlantaService();
        $this->botiquinService = new BotiquinService();
        $this->userLocationRepository = new UserLocationRepository();
        $this->authService = new AuthService();
    }

    public function index(): void
    {
        // Verificar permisos - solo admins pueden ver todos los usuarios
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL']);

        // Obtener la lista de usuarios
        $users = $this->userService->getAllUsers();

        $data = [
            'users' => $users,
            'success' => $_GET['success'] ?? null,
            'error' => $_GET['error'] ?? null,
            'roleService' => $this->roleService,  // Asegurar que roleService esté disponible
            'title' => 'Gestión de Usuarios',
            'navTitle' => 'Usuarios'
        ];

        $this->render('entity.users.dashboard_user', $data);
    }

    public function create(): void
    {
        // Verificar permisos - solo admins pueden crear usuarios
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL']);

        // Obtener los valores de option de los selects
        $roles = $this->roleService->getAllRoles();

        $data = [
            'title' => 'Crear Usuario',
            'navTitle' => 'Crear Usuario',
            'roles' => $roles,
            'success' => false,
            'errors' => [],
            'input' => []
        ];

        // Procesar el envío del formulario
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->handleCreate($data);
        } else {
            $this->render('entity.users.create_user', $data);
        }
    }

    private function handleCreate(array &$data): void
    {
        $input = [
            'nombre' => filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_SPECIAL_CHARS),
            'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
            'password' => filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS),
            'confirm_password' => filter_input(INPUT_POST, 'confirm_password', FILTER_SANITIZE_SPECIAL_CHARS),
            'role' => filter_input(INPUT_POST, 'role', FILTER_SANITIZE_NUMBER_INT)
        ];

        $data['input'] = $input;
        $errors = [];

        if ($input['password'] !== $input['confirm_password']) {
            $errors['confirm_password'] = "Las contraseñas no coinciden.";
        }

        // Validar los datos de entrada
        if (empty($errors)) {
            try {
                $success = $this->authService->register(
                    $input['nombre'],
                    $input['email'],
                    $input['password'],
                    $input['role']
                );

                if ($success) {
                    // Obtener el ID del usuario recién creado
                    $userId = $this->authService->getUserIdByEmail($input['email']);

                    // Redirigir a la página de dashboard con mensaje de éxito
                    $this->redirect('users', ['success' => 'created']);
                    return;
                }

            } catch (\InvalidArgumentException $e) {
                // Capturar errores de validación
                $errors['general'] = $e->getMessage();
            } catch (Exception $e) {
                // Capturar otros errores
                $errors['general'] = "Error al registrar el usuario: " . $e->getMessage();
            }
        }

        $data['errors'] = $errors;
        $this->render('entity.users.create_user', $data);
    }

    public function edit(): void
    {
        // Verificar permisos - solo admins pueden editar usuarios
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL']);

        $userId = $_GET['id'] ?? null;

        if (!$userId || !is_numeric($userId)) {
            $this->redirect('users', ['error' => 'id_invalid']);
            return;
        }

        // Obtener el usuario a editar
        $user = $this->userService->getUserById((int)$userId);
        if (!$user) {
            $this->redirect('users', ['error' => 'user_not_found']);
            return;
        }

        // Obtener roles para el selector
        $roles = $this->roleService->getAllRoles();

        $data = [
            'title' => 'Editar Usuario',
            'navTitle' => 'Editar Usuario',
            'user' => $user,
            'roles' => $roles,
            'errors' => [],
            'success' => false
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->handleEdit((int)$userId, $data);
        } else {
            $this->render('entity.users.edit_user', $data);
        }
    }

    private function handleEdit(int $userId, array &$data): void
    {
        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_NUMBER_INT);

        $errors = [];

        if (empty($nombre) || empty($email) || empty($role)) {
            $errors['general'] = "Todos los campos son obligatorios.";
        }

        // Verificar si el email es único (excluyendo el usuario actual)
        $existingUser = $this->userService->getUserByEmail($email);
        if ($existingUser && $existingUser->getId() != $userId) {
            $errors['email'] = "El correo electrónico ya está en uso.";
        }

        if (empty($errors)) {
            try {
                // Corregir: usar userService en lugar de userRepository
                if ($this->userService->updateUser($userId, $nombre, $email, $role)) {
                    $this->redirect('users', ['success' => 'updated']);
                    return;
                } else {
                    $errors['general'] = "Error al actualizar el usuario.";
                }
            } catch (Exception $e) {
                $errors['general'] = "Error: " . $e->getMessage();
            }
        }

        $data['errors'] = $errors;
        $data['user']->setNombre($nombre);
        $data['user']->setEmail($email);
        $data['user']->setRol($role);
        $this->render('entity.users.edit_user', $data);
    }

    public function delete(): void
    {
        // Verificar permisos - solo admins pueden eliminar usuarios
        AuthMiddleware::requireRole(['ADMINISTRADOR']);

        $userId = $_GET['id'] ?? null;

        if (!$userId || !is_numeric($userId)) {
            $this->redirect('users', ['error' => 'id_invalid']);
            return;
        }

        // Verificar que no se intente eliminar el usuario actual
        if ((int)$userId === $this->getCurrentUserId()) {
            $this->redirect('users', ['error' => 'cannot_delete_self']);
            return;
        }

        // Obtener el usuario a eliminar
        $user = $this->userService->getUserById((int)$userId);
        if (!$user) {
            $this->redirect('users', ['error' => 'user_not_found']);
        }

        $data = [
            'title' => 'Eliminar Usuario',
            'navTitle' => 'Eliminar Usuario',
            'user' => $user,
            'roleName' => $this->roleService->getRoleById($user->getRol())->getNombre()
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->handleDelete((int)$userId);
        } else {
            $this->render('entity.users.delete_user', $data);
        }
    }

    private function handleDelete(int $userId): void
    {
        try {
            // Primero eliminar todas las ubicaciones asignadas
            $this->userLocationRepository->deleteAllUserLocations($userId);

            // Luego eliminar el usuario - corregir: usar userService en lugar de userRepository
            if ($this->userService->deleteUser($userId)) {
                $this->redirect('users', ['success' => 'deleted']);
            } else {
                $this->redirect('users', ['error' => 'delete_failed']);
            }
        } catch (Exception $e) {
            $this->redirect('users', ['error' => urlencode($e->getMessage())]);
        }
    }

    public function locations(): void
    {
        // Verificar permisos - solo admins pueden asignar ubicaciones
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL']);

        $userId = $_GET['user_id'] ?? null;

        if (!$userId || !is_numeric($userId)) {
            $this->redirect('/users', ['error' => 'id_invalid']);
        }

        // Obtener el usuario
        $user = $this->userService->getUserById((int)$userId);
        if (!$user) {
            $this->redirect('/users', ['error' => 'user_not_found']);
        }

        // Obtener listas de hospitales, plantas y botiquines
        $hospitales = $this->hospitalService->getAllHospitals();
        $plantas = $this->plantaService->getAllArray();
        $botiquines = $this->botiquinService->getAllBotiquines();

        // Obtener ubicaciones ya asignadas al usuario
        $assignedHospitals = $this->userService->getUserHospitals($userId);
        $assignedPlantas = $this->userService->getUserPlantas($userId);
        $assignedBotiquines = $this->userService->getUserBotiquines($userId);

        // Determinar el tipo de ubicación según el rol del usuario
        $userRole = $user->getRol();
        $locationType = '';

        switch ($userRole) {
            case '1': // ADMINISTRADOR
            case '2': // GESTOR_GENERAL
                $locationType = 'admin';
                break;
            case '3': // GESTOR_HOSPITAL
                $locationType = 'hospitales';
                break;
            case '4': // GESTOR_PLANTA
                $locationType = 'plantas';
                break;
            case '5': // GESTOR_BOTIQUIN
                $locationType = 'botiquines';
                break;
            default:
                break;
        }

        $data = [
            'title' => 'Asignar Ubicaciones',
            'navTitle' => 'Asignar Ubicaciones',
            'user' => $user,
            'hospitales' => $hospitales,
            'plantas' => $plantas,
            'botiquines' => $botiquines,
            'assignedHospitals' => $assignedHospitals,
            'assignedPlantas' => $assignedPlantas,
            'assignedBotiquines' => $assignedBotiquines,
            'locationType' => $locationType,
            'success' => false,
            'error' => false,
            'scripts' => ["toasts.js", "user_locations.js"]
        ];

        // Procesar solicitud de guardar ubicaciones
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_locations'])) {
            $this->handleSaveLocations((int)$userId, $data);
        } else {
            $this->render('entity.users.user_locations', $data);
        }
    }

    private function handleSaveLocations(int $userId, array &$data): void
    {
        try {
            // Eliminar todas las ubicaciones existentes
            $this->userLocationRepository->deleteAllUserLocations($userId);

            // Procesar según el tipo de ubicación
            switch ($data['locationType']) {
                case 'hospitales':
                    if (isset($_POST['hospital_ids']) && is_array($_POST['hospital_ids'])) {
                        foreach ($_POST['hospital_ids'] as $hospitalId) {
                            $this->userLocationRepository->addUserHospital($userId, $hospitalId);
                        }
                    }
                    break;
                case 'plantas':
                    if (isset($_POST['planta_ids']) && is_array($_POST['planta_ids'])) {
                        foreach ($_POST['planta_ids'] as $plantaId) {
                            $this->userLocationRepository->addUserPlanta($userId, $plantaId);
                        }
                    }
                    break;
                case 'botiquines':
                    if (isset($_POST['botiquin_ids']) && is_array($_POST['botiquin_ids'])) {
                        foreach ($_POST['botiquin_ids'] as $botiquinId) {
                            $this->userLocationRepository->addUserBotiquin($userId, $botiquinId);
                        }
                    }
                    break;
            }

            $data['success'] = true;

            // Recargar las ubicaciones asignadas
            $data['assignedHospitals'] = $this->userService->getUserHospitals($userId);
            $data['assignedPlantas'] = $this->userService->getUserPlantas($userId);
            $data['assignedBotiquines'] = $this->userService->getUserBotiquines($userId);

        } catch (Exception $e) {
            $data['error'] = $e->getMessage();
            error_log("Error al guardar ubicaciones: " . $e->getMessage());
        }

        $this->render('entity.users.user_locations', $data);
    }
}
