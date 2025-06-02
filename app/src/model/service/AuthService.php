<?php

namespace model\service;

use Exception;
use JetBrains\PhpStorm\NoReturn;

class AuthService
{
    private UserService $userService;
    private RoleService $roleService;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->roleService = new RoleService();
    }

    /**
     * Autentica a un usuario con su email y contraseña
     * 
     * @param string $email Email del usuario
     * @param string $password Contraseña del usuario
     * @return bool True si la autenticación fue exitosa, false en caso contrario
     */
    public function login(string $email, string $password): bool
    {
        $user = $this->userService->getUserByEmail($email);
        if ($user) {
            // Verificar la contraseña con el hash almacenado
            if (password_verify($password, $user->getPassword())) {
                // Iniciar la sesión si no está ya iniciada
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                
                // Almacenar información del usuario en la sesión
                $_SESSION['user_id'] = $user->getId();
                $_SESSION['user_name'] = $user->getNombre();
                $_SESSION['user_email'] = $user->getEmail();
                // Almacenar el rol del usuario llamando al meto do getRolById del RoleService
                $rol = $this->roleService->getRoleById($user->getRol());
                $_SESSION['user_role'] = $rol ? $rol->getNombre() : 'Invitado';
                
                // Guardar la hora de inicio de sesión
                $_SESSION['login_time'] = time();
                
                // Si la contraseña necesita rehashing (por cambios en algoritmo)
                if (password_needs_rehash($user->getPassword(), PASSWORD_DEFAULT)) {
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    // Actualizar la contraseña en la base de datos
                    $this->userService->updatePassword($user->getId(), $newHash);
                }

                return true;
            }
        }
        return false;
    }

    /**
     * Registra un nuevo usuario en el sistema
     * 
     * @param string $nombre Nombre del usuario
     * @param string $email Email del usuario
     * @param string $password Contraseña del usuario
     * @param string $rol Rol del usuario
     * @return bool True si el registro fue exitoso, false en caso contrario
     * @throws Exception Si hay un error en la validación de datos
     */
    public function register($nombre, $email, $password, $rol): bool
    {
        if (empty($nombre) || empty($email) || empty($password)) {
            throw new Exception("Todos los campos son obligatorios.");
        }

        if ($this->userService->getUserByEmail($email)) {
            throw new Exception("Ya existe un usuario con ese email.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("El email no es válido.");
        }

        if (strlen($password) < 6) {
            throw new Exception("La contraseña debe tener al menos 6 caracteres.");
        }

        // Hash the password before saving
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        return $this->userService->addUser($nombre, $email, $passwordHash, $rol);
    }

    /**
     * Cierra la sesión del usuario y lo redirige a la página de inicio de sesión
     */
    #[NoReturn]
    public function logout(): void
    {
        // Destruir la sesión
        session_destroy();
        
        // Redirigir a la página de inicio de sesión con mensaje de éxito
        header("Location: /login?success=logout_success");
        exit();
    }

    /**
     * Verifica si hay un usuario autenticado en la sesión actual
     * 
     * @return bool True si hay un usuario autenticado, false en caso contrario
     */
    public function isAuthenticated(): bool
    {
        return isset($_SESSION['user_id']);
    }
}
