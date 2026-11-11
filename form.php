<?php

$daftarKategori = ["Web Development", "Mobile Development", "Design UI/UX"];
$daftarPeserta = ["Individu", "Tim"];

$nama = $email = $hp = $umur = $github = "";
$kategori = $peserta = "";

$errors = [];
$sukses = false;

function err($errors, $k)
{
    if (isset($errors[$k])) {
        return "<small style='color:red'>" . htmlspecialchars($errors[$k], ENT_QUOTES, 'UTF-8') . "</small>";
    }

    return "";
}

function aman($data)
{
    return htmlspecialchars((string) $data, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama = trim($_POST['nama'] ?? "");
    $email = trim($_POST['email'] ?? "");
    $hp = trim($_POST['hp'] ?? "");
    $umur = trim($_POST['umur'] ?? "");
    $github = trim($_POST['github'] ?? "");

    $kategori = $_POST['kategori'] ?? "";
    $peserta = $_POST['peserta'] ?? "";

    if ($nama === "") {
        $errors['nama'] = "Nama wajib diisi.";
    } elseif (strlen($nama) < 3 || strlen($nama) > 50) {
        $errors['nama'] = "Nama harus 3-50 karakter.";
    }

    if ($email === "") {
        $errors['email'] = "Email wajib diisi.";
    } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $errors['email'] = "Format email tidak valid.";
    }

    $opsiHP = [
        "options" => [
            "regexp" => "/^08[0-9]{8,11}$/"
        ]
    ];

    if ($hp === "") {
        $errors['hp'] = "Nomor HP wajib diisi.";
    } elseif (filter_var($hp, FILTER_VALIDATE_REGEXP, $opsiHP) === false) {
        $errors['hp'] = "Nomor HP harus diawali 08 dan berisi 10-13 digit.";
    }

    $opsiUmur = [
        "options" => [
            "min_range" => 17,
            "max_range" => 19
        ]
    ];

    if ($umur === "") {
        $errors['umur'] = "Umur wajib diisi.";
    } elseif (filter_var($umur, FILTER_VALIDATE_INT, $opsiUmur) === false) {
        $errors['umur'] = "Umur harus berupa angka bulat antara 17-19 tahun.";
    }

    if ($github !== "") {

        if (filter_var($github, FILTER_VALIDATE_URL) === false) {
            $errors['github'] = "URL GitHub tidak valid.";
        } else {
            $host = strtolower(parse_url($github, PHP_URL_HOST) ?? "");
            $scheme = strtolower(parse_url($github, PHP_URL_SCHEME) ?? "");

            if ($host !== "github.com" || !in_array($scheme, ["http", "https"], true)) {
                $errors['github'] = "Masukkan URL profil dari github.com.";
            }
        }
    }

    if (!in_array($kategori, $daftarKategori, true)) {
        $errors['kategori'] = "Silakan pilih kategori yang tersedia.";
    }

    if (!in_array($peserta, $daftarPeserta, true)) {
        $errors['peserta'] = "Silakan pilih jenis peserta.";
    }

    $sukses = empty($errors);
}

?>

<h2>Registrasi Lomba Coding</h2>

<?php if ($sukses): ?>

    <h3>Registrasi Berhasil!</h3>

    <p>Nama: <?= aman($nama) ?></p>
    <p>Email: <?= aman($email) ?></p>
    <p>Nomor HP: <?= aman($hp) ?></p>
    <p>Umur: <?= aman($umur) ?> tahun</p>
    <p>GitHub: <?= aman($github ?: '-') ?></p>
    <p>Kategori: <?= aman($kategori) ?></p>
    <p>Peserta: <?= aman($peserta) ?></p>

<?php else: ?>

    <form method="POST" action="" novalidate>

        <p>
            Nama:
            <br>
            <input type="text" name="nama" value="<?= aman($nama) ?>">
            <br>
            <?= err($errors, 'nama') ?>
        </p>

        <p>
            Email:
            <br>
            <input type="email" name="email" value="<?= aman($email) ?>">
            <br>
            <?= err($errors, 'email') ?>
        </p>

        <p>
            Nomor HP:
            <br>
            <input type="text" name="hp" value="<?= aman($hp) ?>">
            <br>
            <?= err($errors, 'hp') ?>
        </p>

        <p>
            Umur:
            <br>
            <input type="number" name="umur" value="<?= aman($umur) ?>">
            <br>
            <?= err($errors, 'umur') ?>
        </p>

        <p>
            URL GitHub (opsional):
            <br>
            <input type="text" name="github" value="<?= aman($github) ?>">
            <br>
            <?= err($errors, 'github') ?>
        </p>

        <p>
            Kategori:
            <br>
            <select name="kategori">
                <option value="">-- Pilih Kategori --</option>

                <?php foreach ($daftarKategori as $item): ?>
                    <option value="<?= aman($item) ?>"
                        <?= $kategori === $item ? "selected" : "" ?>>
                        <?= aman($item) ?>
                    </option>
                <?php endforeach; ?>

            </select>
            <br>
            <?= err($errors, 'kategori') ?>
        </p>

        <p>Jenis Peserta:</p>

        <?php foreach ($daftarPeserta as $item): ?>

            <label>
                <input
                    type="radio"
                    name="peserta"
                    value="<?= aman($item) ?>"
                    <?= $peserta === $item ? "checked" : "" ?>>
                <?= aman($item) ?>
            </label>

        <?php endforeach; ?>

        <br>
        <?= err($errors, 'peserta') ?>

        <p>
            <button type="submit">Daftar</button>
        </p>

    </form>

<?php endif; ?>