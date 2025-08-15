<?php

namespace App\Controllers;

use App\Entity\User;
use App\Middleware\AuthMiddleware;
use App\Models\UserModel;

class UserController extends ParentController
{
    private UserModel $userModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
    }
    
    public function register()
    {
        $errors = [];
        $success = false;
        
        // If user is already logged in, redirect to home
        if (AuthMiddleware::isLoggedIn()) {
            header('Location: /');
            exit;
        }
        
        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            // Validate inputs
            if (empty($username)) {
                $errors[] = 'Username is required';
            }
            
            if (empty($email)) {
                $errors[] = 'Email is required';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email format';
            }
            
            if (empty($password)) {
                $errors[] = 'Password is required';
            } elseif (strlen($password) < 6) {
                $errors[] = 'Password must be at least 6 characters';
            }
            
            if ($password !== $confirmPassword) {
                $errors[] = 'Passwords do not match';
            }
            
            // Check if username or email already exists
            if (!empty($username) && $this->userModel->getUserByUsername($username)) {
                $errors[] = 'Username already exists';
            }
            
            if (!empty($email) && $this->userModel->getUserByEmail($email)) {
                $errors[] = 'Email already exists';
            }
            
            // If no errors, create the user
            if (empty($errors)) {
                $user = new User();
                $user->setUsername($username)
                    ->setEmail($email)
                    ->setPassword($password)
                    ->setRole('user')
                    ->setIsActive(true);
                
                if ($this->userModel->createUser($user)) {
                    $success = true;
                } else {
                    $errors[] = 'Failed to create user';
                }
            }
        }
        
        $this->render('user/register', [
            'errors' => $errors,
            'success' => $success,
        ]);
    }
    
    public function login()
    {
        $errors = [];
        
        // If user is already logged in, redirect to home
        if (AuthMiddleware::isLoggedIn()) {
            header('Location: /');
            exit;
        }
        
        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            // Validate inputs
            if (empty($email)) {
                $errors[] = 'Email is required';
            }
            
            if (empty($password)) {
                $errors[] = 'Password is required';
            }
            
            // If no errors, attempt to login
            if (empty($errors)) {
                $user = $this->userModel->getUserByEmail($email);
                
                if (!$user) {
                    $errors[] = 'Invalid email or password';
                } elseif (!$this->userModel->verifyPassword($user, $password)) {
                    $errors[] = 'Invalid email or password';
                } elseif (!$user->getIsActive()) {
                    $errors[] = 'Your account is not active';
                } else {
                    // Login successful
                    AuthMiddleware::login(
                        $user->getId(),
                        $user->getUsername(),
                        $user->getEmail(),
                        $user->getRole()
                    );
                    
                    // Redirect to home page
                    header('Location: /');
                    exit;
                }
            }
        }
        
        $this->render('user/login', [
            'errors' => $errors,
        ]);
    }
    
    public function logout()
    {
        AuthMiddleware::logout();
        header('Location: /');
        exit;
    }
    
    public function profile()
    {
        // Require login
        AuthMiddleware::requireLogin();
        
        $user = $this->userModel->getUser($_SESSION['user_id']);
        $errors = [];
        $success = false;
        
        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            
            // Validate inputs
            if (empty($username)) {
                $errors[] = 'Username is required';
            }
            
            if (empty($email)) {
                $errors[] = 'Email is required';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email format';
            }
            
            // Check if username or email already exists (excluding current user)
            $existingUser = $this->userModel->getUserByUsername($username);
            if (!empty($username) && $existingUser && $existingUser->getId() !== $user->getId()) {
                $errors[] = 'Username already exists';
            }
            
            $existingUser = $this->userModel->getUserByEmail($email);
            if (!empty($email) && $existingUser && $existingUser->getId() !== $user->getId()) {
                $errors[] = 'Email already exists';
            }
            
            // If no errors, update the user
            if (empty($errors)) {
                $user->setUsername($username)
                    ->setEmail($email);
                
                if ($this->userModel->updateUser($user)) {
                    $success = true;
                    
                    // Update session
                    $_SESSION['user_username'] = $username;
                    $_SESSION['user_email'] = $email;
                } else {
                    $errors[] = 'Failed to update profile';
                }
            }
        }
        
        $this->render('user/profile', [
            'user' => $user,
            'errors' => $errors,
            'success' => $success,
        ]);
    }
}