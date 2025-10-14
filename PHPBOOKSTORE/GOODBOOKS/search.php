<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Search - GoodBooks</title>
  <link rel="stylesheet" href="css/bootstrap.min.css" />
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: blueviolet;">
  <div class="container">
    <a class="navbar-brand" href="#">
      <img src="img/book-icon-134.png" width="50" height="50" alt="Book Icon">
      <span style="font-weight: bold; font-size: 1rem; color: white;">GoodBooks</span>
    </a>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="GoodBooks.php">Home</a></li>
        <li class="nav-item"><a class="nav-link active" href="search.php">Search</a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
    <li class="nav-item">
        <a class="nav-link" href="profile.php">Your Profile</a>
    </li>
<?php else: ?>
    <li class="nav-item">
        <a class="nav-link" href="login_register.php">Login / Register</a>
    </li>
<?php endif; ?>
        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-5">
  <form method="GET" action="SearchBuku.php">
    <div class="input-group mb-3">
      <input type="text" name="keyword" class="form-control" placeholder="Search books..." required>
      <select name="filter" class="form-select" style="max-width: 200px;">
        <option value="judul">Judul</option>
        <option value="kategori">Kategori</option>
        <option value="penulis">Penulis</option>
        <option value="penerbit">Penerbit</option>
        <option value="franchise">Franchise</option>
      </select>
      <button class="btn btn-primary" type="submit">Search</button>
    </div>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
