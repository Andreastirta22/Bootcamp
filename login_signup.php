<?php
// Konfigurasi database
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'bc_program';

// Menghubungkan ke database
$connection = new mysqli($host, $username, $password, $database);
if ($connection->connect_error) {
    die("Koneksi database gagal: " . $connection->connect_error);
}

// Fungsi untuk memeriksa apakah email sudah terdaftar
function isEmailRegistered($email)
{
    global $connection;

    // Melakukan sanitasi input pengguna untuk mencegah serangan injeksi SQL
    $email = $connection->real_escape_string($email);

    // Mencari pengguna dengan email yang sesuai
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = $connection->query($query);

    if ($result->num_rows === 1) {
        return true;
    }

    return false;
}

// Fungsi untuk menambahkan pengguna baru ke database
function addUser($name, $email, $password)
{
    global $connection;

    // Melakukan sanitasi input pengguna untuk mencegah serangan injeksi SQL
    $name = $connection->real_escape_string($name);
    $email = $connection->real_escape_string($email);
    $password = $connection->real_escape_string($password);

    // Membuat hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Menambahkan pengguna baru ke database
    $query = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hashedPassword')";
    $result = $connection->query($query);

    if ($result) {
        return true;
    }

    return false;
}

// Fungsi untuk memeriksa apakah email dan password benar
function authenticateUser($email, $password)
{
    global $connection;

    // Melakukan sanitasi input pengguna untuk mencegah serangan injeksi SQLchr
    $email = $connection->real_escape_string($email);
    $password = $connection->real_escape_string($password);

    // Mencari pengguna dengan email yang sesuai
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = $connection->query($query);

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Memeriksa apakah password benar
        if (password_verify($password, $user['password'])) {
            // Login berhasil, simpan informasi pengguna dalam sesi (session)
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            // Alihkan ke halaman utama setelah login berhasil
            header("Location: home.php");
            exit();
        }
    }

    // Jika email atau password tidak valid, kembali ke halaman login dengan pesan error
    header("Location: login_signup.php?error=email atau password tidak valid");
    exit();
}

// Memproses data yang dikirimkan oleh form signup
if (isset($_POST['signup_name']) && isset($_POST['signup_email']) && isset($_POST['signup_password'])) {
    $name = $_POST['signup_name'];
    $email = $_POST['signup_email'];
    $password = $_POST['signup_password'];

    // Memeriksa apakah email sudah terdaftar sebelumnya
    if (isEmailRegistered($email)) {
        header("Location: login_signup.php?error=Email already registered");
        exit();
    }

    // Menambahkan pengguna baru ke database
    if (addUser($name, $email, $password)) {
        // Pengguna berhasil ditambahkan, langsung lakukan login
        authenticateUser($email, $password);
    } else {
        header("Location: login_signup.php?error=Failed to register user");
        exit();
    }
}

// Memproses data yang dikirimkan oleh form login
if (isset($_POST['login_email']) && isset($_POST['login_password'])) {
    $email = $_POST['login_email'];
    $password = $_POST['login_password'];

    authenticateUser($email, $password);
}

// Menampilkan pesan kesalahan jika ada
if (isset($_GET['error'])) {
    $errorMessage = $_GET['error'];
    echo "<p class='error-message'>$errorMessage</p>";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="form-structor">
        <div class="signup">
            <h2 class="form-title" id="signup"><span>or</span>Sign up</h2>
            <div class="form-holder">
                <form action="" method="POST">
                    <input type="text" name="signup_name" class="input" placeholder="Name" required />
                    <input type="email" name="signup_email" class="input" placeholder="Email" required />
                    <input type="password" name="signup_password" class="input" placeholder="Password" required />
                    <button type="submit" class="submit-btn">Sign up</button>
                </form>
            </div>
        </div>
        <div class="login slide-up">
            <div class="center">
                <h2 class="form-title" id="login"><span>or</span>Log in</h2>
                <div class="form-holder">
                    <form action="" method="POST">
                        <input type="email" name="login_email" class="input" placeholder="Email" required />
                        <input type="password" name="login_password" class="input" placeholder="Password" required />
                        <button type="submit" class="submit-btn">Log in</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="loading" style="display: none;">Loading...</div>
    <script src="js/script.js"></script>

</body>

</html>