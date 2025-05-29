<?php

namespace controllers;

use Exception;
use JetBrains\PhpStorm\NoReturn;
use middleware\AuthMiddleware;
use model\service\AuthService;
use model\service\UserService;

class AuthController extends BaseController
{
    private UserService $userService;
    private AuthService $authService;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->authService = new AuthService();
    }

    public function login(): void
    {
        // Si ya está autenticado, redirigir a la página principal
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleLogin();
        } else {
            $data = [
                'title' => 'Iniciar Sesión',
                'navTitle' => 'Autenticación',
                'error' => $_GET['error'] ?? null,
                'success' => $_GET['success'] ?? null,
                'redirect' => $_GET['redirect'] ?? '/'
                // Se eliminó la opción hideNav
            ];

            // Renderizar la vista de login usando notación de punto
            $this->render('auth.login', $data);
        }
    }

    public function register(): void
    {
        // Verificar permisos - solo administradores pueden registrar usuarios
        AuthMiddleware::requireRole(['ADMINISTRADOR']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleRegister();
        } else {
            $data = [
                'title' => 'Registrar Usuario',
                'navTitle' => 'Registro',
                'error' => $_GET['error'] ?? null,
                'success' => $_GET['success'] ?? null
            ];

            // Renderizar la vista de registro usando notación de punto
            $this->render('auth.register', $data);
        }
    }

    #[NoReturn]
    public function logout(): void
    {
        // Usar el método logout de AuthService en lugar de manejar la sesión directamente
        $this->authService->logout();
    }

    public function changePassword(): void
    {
        // Verificar que el usuario esté autenticado
        AuthMiddleware::requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePasswordChange();
        } else {
            $data = [
                'title' => 'Cambiar Contraseña',
                'navTitle' => 'Cambiar Contraseña',
                'error' => $_GET['error'] ?? null,
                'success' => $_GET['success'] ?? null
            ];

            // Renderizar la vista de cambio de contraseña usando notación de punto
            $this->render('auth.change_password', $data);
        }
    }

    private function handleLogin(): void
    {
        try {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $redirect = $_POST['redirect'] ?? '/';
            
            if (empty($email) || empty($password)) {
                throw new Exception('Todos los campos son obligatorios');
            }
            
            // Intentar autenticar al usuario usando AuthService
            $success = $this->authService->login($email, $password);
            
            if (!$success) {
                throw new Exception('Credenciales incorrectas');
            }
            
            // Si llegamos aquí, la autenticación fue exitosa
            // La sesión ya está configurada por AuthService::login()
            
            // Redirigir a la página solicitada o a la página principal
            $this->redirect($redirect);
            
        } catch (Exception $e) {
            // En caso de error, mostrar mensaje y volver al formulario
            $this->redirect('/login', [
                'error' => urlencode($e->getMessage()),
                'redirect' => $redirect
            ]);
        }
    }
    
    private function handleRegister(): void
    {
        try {
            $nombre = $_POST['nombre'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $rol = $_POST['rol'] ?? '';
            
            // Validaciones
            if (empty($nombre) || empty($email) || empty($password) || empty($confirmPassword) || empty($rol)) {
                throw new Exception('Todos los campos son obligatorios');
            }
            
            if ($password !== $confirmPassword) {
                throw new Exception('Las contraseñas no coinciden');
            }
            
            // Registrar al nuevo usuario usando AuthService
            $success = $this->authService->register($nombre, $email, $password, $rol);
            
            if (!$success) {
                throw new Exception('No se pudo registrar el usuario');
            }
            
            // Redirigir con mensaje de éxito
            $this->redirect('/register', ['success' => 'user_registered']);
            
        } catch (Exception $e) {
            // En caso de error, mostrar mensaje y volver al formulario
            $this->redirect('/register', ['error' => urlencode($e->getMessage())]);
        }
    }
    
    private function handlePasswordChange(): void
    {
        try {
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            // Validaciones
            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                throw new Exception('Todos los campos son obligatorios');
            }
            
            if ($newPassword !== $confirmPassword) {
                throw new Exception('Las nuevas contraseñas no coinciden');
            }
            
            // Obtener el ID de usuario actual
            $userId = $this->getCurrentUserId();
            
            // Verificar la contraseña actual
            $user = $this->userService->getUserById($userId);
            if (!$user || !password_verify($currentPassword, $user->getPassword())) {
                throw new Exception('La contraseña actual es incorrecta');
            }
            
            // Hash de la nueva contraseña
            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            
            // Actualizar la contraseña en la base de datos
            $success = $this->userService->updatePassword($userId, $newPasswordHash);
            
            if (!$success) {
                throw new Exception('No se pudo actualizar la contraseña');
            }
            
            // Redirigir con mensaje de éxito
            $this->redirect('/password/change', ['success' => 'password_changed']);
            
        } catch (Exception $e) {
            // En caso de error, mostrar mensaje y volver al formulario
            $this->redirect('/password/change', ['error' => urlencode($e->getMessage())]);
        }
    }
}
