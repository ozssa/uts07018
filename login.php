<?php
session_start();
// Redirect jika user sudah login
if(isset($_SESSION['admin'])) {
    header("Location: pages/ajaxMahasiswa.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="Halaman Login Sistem Informasi Akademik">
    <title>Login Admin - SIA</title>
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/lib/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" 
        crossorigin="anonymous" 
        referrerpolicy="no-referrer" />
</head>
<body class="bg-light">
    <div class="container vh-100">
        <div class="row justify-content-center align-items-center h-100">
            <div class="col-md-5 col-xxl-4">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white py-3">
                        <h1 class="text-center mb-0 fs-4">
                            <i class="fas fa-lock me-2" aria-hidden="true"></i>LOGIN ADMIN
                        </h1>
                    </div>
                    
                    <div class="card-body p-4">
                        <?php if(isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2" aria-hidden="true"></i>
                            <?= htmlspecialchars($_SESSION['error']) ?>
                            <button type="button" 
                                class="btn-close" 
                                data-bs-dismiss="alert"
                                aria-label="Tutup"></button>
                        </div>
                        <?php unset($_SESSION['error']); endif; ?>

                        <form action="proses_login.php" method="POST" autocomplete="on">
                            <div class="mb-3">
                                <label for="username" class="form-label fw-medium">
                                    <i class="fas fa-user me-1" aria-hidden="true"></i>Username
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="username" 
                                    name="username" 
                                    required
                                    autocomplete="username"
                                    autofocus
                                    aria-describedby="usernameHelp">
                                <div id="usernameHelp" class="form-text">Masukkan username admin</div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label fw-medium">
                                    <i class="fas fa-key me-1" aria-hidden="true"></i>Password
                                </label>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="password" 
                                    name="password" 
                                    required
                                    autocomplete="current-password"
                                    aria-describedby="passwordHelp">
                                <div id="passwordHelp" class="form-text">Password bersifat case-sensitive</div>
                            </div>

                            <div class="d-grid">
                                <button 
                                    type="submit" 
                                    class="btn btn-primary btn-lg fw-medium"
                                    aria-label="Masuk ke sistem">
                                    <i class="fas fa-sign-in-alt me-2" aria-hidden="true"></i>Masuk
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="assets/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" 
        integrity="sha512-fD9DI5bZwQxOi7MhYWnnNPlvXdp/2Pj3XSTRrFs5FQa4mizyGLnJcN6tuvUS6LbmgN1ut+XGSABKvjN0H6Aoow==" 
        crossorigin="anonymous" 
        referrerpolicy="no-referrer"></script>
</body>
</html>