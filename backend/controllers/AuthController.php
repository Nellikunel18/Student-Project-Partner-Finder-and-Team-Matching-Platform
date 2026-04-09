<?php
require_once '../config/db.php';

class AuthController {

    public static function register() {
        global $conn;

        $data = json_decode(file_get_contents("php://input"), true);

        $name = $data['name'];
        $email = $data['email'];
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        $role = $data['role'];

        $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $password, $role);

        if ($stmt->execute()) {
            echo json_encode(["message" => "User registered"]);
        } else {
            echo json_encode(["error" => "Registration failed"]);
        }
    }

    public static function login() {
        global $conn;

        $data = json_decode(file_get_contents("php://input"), true);

        $email = $data['email'];
        $password = $data['password'];

        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            echo json_encode(["message" => "Login success", "user" => $user]);
        } else {
            echo json_encode(["error" => "Invalid credentials"]);
        }
    }
}
?>