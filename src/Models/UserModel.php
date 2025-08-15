<?php

namespace App\Models;

use App\Database\Database;
use App\Entity\User;
use PDO;

class UserModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function getUser(int $id): ?User
    {
        $query = $this->pdo->prepare("SELECT * FROM user WHERE id = :id");
        $query->execute([
            "id" => $id,
        ]);
        $userData = $query->fetch();
        
        if (!$userData) {
            return null;
        }
        
        return $this->createUserFromData($userData);
    }

    public function getUserByEmail(string $email): ?User
    {
        $query = $this->pdo->prepare("SELECT * FROM user WHERE email = :email");
        $query->execute([
            "email" => $email,
        ]);
        $userData = $query->fetch();
        
        if (!$userData) {
            return null;
        }
        
        return $this->createUserFromData($userData);
    }

    public function getUserByUsername(string $username): ?User
    {
        $query = $this->pdo->prepare("SELECT * FROM user WHERE username = :username");
        $query->execute([
            "username" => $username,
        ]);
        $userData = $query->fetch();
        
        if (!$userData) {
            return null;
        }
        
        return $this->createUserFromData($userData);
    }

    public function createUser(User $user): bool
    {
        // Hash the password before storing
        $hashedPassword = password_hash($user->getPassword(), PASSWORD_DEFAULT);
        
        $query = $this->pdo->prepare("
            INSERT INTO user (username, email, password, role, created_at, is_active) 
            VALUES (:username, :email, :password, :role, :created_at, :is_active)
        ");
        
        $now = new \DateTime();
        $dateStr = $now->format('Y-m-d H:i:s');
        
        return $query->execute([
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'password' => $hashedPassword,
            'role' => $user->getRole(),
            'created_at' => $dateStr,
            'is_active' => $user->getIsActive() ? 1 : 0,
        ]);
    }

    public function updateUser(User $user): bool
    {
        $query = $this->pdo->prepare("
            UPDATE user 
            SET username = :username, 
                email = :email, 
                role = :role, 
                is_active = :is_active
            WHERE id = :id
        ");
        
        return $query->execute([
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'role' => $user->getRole(),
            'is_active' => $user->getIsActive() ? 1 : 0,
        ]);
    }

    public function updatePassword(User $user, string $newPassword): bool
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $query = $this->pdo->prepare("
            UPDATE user 
            SET password = :password
            WHERE id = :id
        ");
        
        return $query->execute([
            'id' => $user->getId(),
            'password' => $hashedPassword,
        ]);
    }

    public function deleteUser(int $id): bool
    {
        $query = $this->pdo->prepare("DELETE FROM user WHERE id = :id");
        return $query->execute(['id' => $id]);
    }

    public function verifyPassword(User $user, string $password): bool
    {
        return password_verify($password, $user->getPassword());
    }

    private function createUserFromData(array $userData): User
    {
        $user = new User();
        $user->setId($userData['id'])
            ->setUsername($userData['username'])
            ->setEmail($userData['email'])
            ->setPassword($userData['password']);
            
        if (isset($userData['role'])) {
            $user->setRole($userData['role']);
        }
        
        if (isset($userData['created_at'])) {
            $user->setCreatedAtFromString($userData['created_at']);
        }
        
        if (isset($userData['is_active'])) {
            $user->setIsActive((bool)$userData['is_active']);
        }
        
        return $user;
    }
}