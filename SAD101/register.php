<?php
session_start();
include_once 'db_connect.php';

// Redirect if already logged in
if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if(empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Please fill in all fields";
    } elseif($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif(strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } else {
        // Check if username or email already exists
        $check_sql = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
        $check_result = $conn->query($check_sql);
        
        if($check_result->num_rows > 0) {
            $error = "Username or email already exists";
        } else {
            // Hash password and insert user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_sql = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$hashed_password', 'customer')";
            
            if($conn->query($insert_sql) === TRUE) {
                $success = "Account created successfully! You can now login.";
            } else {
                $error = "Error creating account: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Café Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .register-container {
            background: linear-gradient(135deg, #432818 0%, #6f4e37 50%, #8b5a3c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            padding: 2rem 0;
        }
        
        .register-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="coffee" patternUnits="userSpaceOnUse" width="20" height="20"><circle cx="10" cy="10" r="2" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23coffee)"/></svg>');
            opacity: 0.3;
        }
        
        .register-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            position: relative;
            z-index: 1;
            max-width: 500px;
            width: 100%;
        }
        
        .register-header {
            background: linear-gradient(135deg, #432818 0%, #6f4e37 100%);
            color: white;
            padding: 2.5rem 2rem 1.5rem;
            text-align: center;
            position: relative;
        }
        
        .cafe-logo {
            font-size: 3rem;
            margin-bottom: 1rem;
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .register-form {
            padding: 2.5rem 2rem;
        }
        
        .form-floating {
            margin-bottom: 1.5rem;
        }
        
        .form-floating > .form-control {
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 1rem 0.75rem;
            height: auto;
            transition: all 0.3s ease;
        }
        
        .form-floating > .form-control:focus {
            border-color: #6f4e37;
            box-shadow: 0 0 0 0.25rem rgba(111, 78, 55, 0.25);
            transform: translateY(-2px);
        }
        
        .form-floating > label {
            color: #6c757d;
            padding: 1rem 0.75rem;
        }
        
        .btn-register {
            background: linear-gradient(135deg, #432818 0%, #6f4e37 100%);
            border: none;
            border-radius: 15px;
            padding: 1rem 2rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(67, 40, 24, 0.4);
        }
        
        .btn-register::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-register:hover::before {
            left: 100%;
        }
        
        .alert-custom {
            border-radius: 15px;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .password-strength {
            margin-top: 0.5rem;
        }
        
        .strength-bar {
            height: 4px;
            border-radius: 2px;
            background-color: #e9ecef;
            overflow: hidden;
        }
        
        .strength-fill {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }
        
        .strength-weak { background-color: #dc3545; width: 25%; }
        .strength-fair { background-color: #fd7e14; width: 50%; }
        .strength-good { background-color: #ffc107; width: 75%; }
        .strength-strong { background-color: #28a745; width: 100%; }
        
        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
        }
        
        .floating-elements::before,
        .floating-elements::after {
            content: '☕';
            position: absolute;
            font-size: 2rem;
            color: rgba(255, 255, 255, 0.1);
            animation: float-around 20s infinite linear;
        }
        
        .floating-elements::before {
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .floating-elements::after {
            top: 60%;
            right: 10%;
            animation-delay: 10s;
        }
        
        @keyframes float-around {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
            100% { transform: translateY(0px) rotate(360deg); }
        }
        
        .terms-checkbox {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="floating-elements"></div>
        
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="register-card mx-auto">
                        <div class="register-header">
                            <div class="cafe-logo">☕</div>
                            <h2 class="mb-0">Join Café Online</h2>
                            <p class="mb-0 opacity-75">Create your account and start ordering</p>
                        </div>
                        
                        <div class="register-form">
                            <?php if(!empty($error)): ?>
                                <div class="alert alert-danger alert-custom">
                                    <i class="bi bi-exclamation-triangle me-2"></i><?php echo $error; ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if(!empty($success)): ?>
                                <div class="alert alert-success alert-custom">
                                    <i class="bi bi-check-circle me-2"></i><?php echo $success; ?>
                                </div>
                            <?php endif; ?>
                            
                            <form action="register.php" method="post" id="registerForm">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                                    <label for="username"><i class="bi bi-person me-2"></i>Username</label>
                                </div>
                                
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                    <label for="email"><i class="bi bi-envelope me-2"></i>Email Address</label>
                                </div>
                                
                                <div class="form-floating">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                                    <label for="password"><i class="bi bi-lock me-2"></i>Password</label>
                                    <div class="password-strength">
                                        <div class="strength-bar">
                                            <div class="strength-fill" id="strengthFill"></div>
                                        </div>
                                        <small class="text-muted" id="strengthText">Password strength</small>
                                    </div>
                                </div>
                                
                                <div class="form-floating">
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                                    <label for="confirm_password"><i class="bi bi-lock-fill me-2"></i>Confirm Password</label>
                                </div>
                                
                                <div class="terms-checkbox">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="terms" required>
                                        <label class="form-check-label" for="terms">
                                            I agree to the <a href="#" style="color: #6f4e37;">Terms of Service</a> and 
                                            <a href="#" style="color: #6f4e37;">Privacy Policy</a>
                                        </label>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-register w-100 text-white">
                                    <i class="bi bi-person-plus me-2"></i>Create Account
                                </button>
                            </form>
                            
                            <div class="text-center mt-4">
                                <p class="mb-0">Already have an account? 
                                    <a href="login.php" class="text-decoration-none fw-bold" style="color: #6f4e37;">
                                        Sign in here
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password strength checker
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthFill = document.getElementById('strengthFill');
            const strengthText = document.getElementById('strengthText');
            
            let strength = 0;
            let text = 'Very Weak';
            
            if (password.length >= 6) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            strengthFill.className = 'strength-fill';
            
            switch(strength) {
                case 0:
                case 1:
                    strengthFill.classList.add('strength-weak');
                    text = 'Weak';
                    break;
                case 2:
                    strengthFill.classList.add('strength-fair');
                    text = 'Fair';
                    break;
                case 3:
                    strengthFill.classList.add('strength-good');
                    text = 'Good';
                    break;
                case 4:
                case 5:
                    strengthFill.classList.add('strength-strong');
                    text = 'Strong';
                    break;
            }
            
            strengthText.textContent = text;
        });
        
        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const terms = document.getElementById('terms').checked;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                showAlert('Passwords do not match', 'danger');
                return;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                showAlert('Password must be at least 6 characters long', 'danger');
                return;
            }
            
            if (!terms) {
                e.preventDefault();
                showAlert('Please accept the terms and conditions', 'danger');
                return;
            }
        });
        
        // Show alert function
        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-custom`;
            alertDiv.innerHTML = `<i class="bi bi-exclamation-triangle me-2"></i>${message}`;
            
            const form = document.getElementById('registerForm');
            form.insertBefore(alertDiv, form.firstChild);
            
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }
        
        // Add floating animation to form elements
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateY(-2px)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'translateY(0)';
            });
        });
        
        // Real-time password confirmation check
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
                this.style.borderColor = '#dc3545';
            } else {
                this.setCustomValidity('');
                this.style.borderColor = '#6f4e37';
            }
        });
    </script>
</body>
</html>
