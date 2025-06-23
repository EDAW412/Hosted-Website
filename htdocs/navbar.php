<?php
$name = $_SESSION['user_name'] ?? '';
$surname = $_SESSION['user_surname'] ?? '';

$userInitials = strtoupper(
    ($name ? $name[0] : '') .
    ($surname ? $surname[0] : '')
);
?>

<link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">

<style>
  .navbar-brand-text {
    font-family: 'Dancing Script', cursive;
    font-size: 1.5rem;
    font-weight: 700;
    color: #000;
    line-height: 1;
  }
</style>

<nav class="navbar navbar-light bg-white shadow-sm sticky-top">
  <div class="container position-relative d-flex align-items-center" style="height: 56px;">
    
    <div style="width: 32px; position: absolute; left: 15px;"></div>
    
    <a href="landing store page.php" class="position-absolute start-50 translate-middle-x d-flex align-items-center text-decoration-none" style="gap: 10px;">
      <img src="images/Laezel_cat.png" alt="Laezel Cat Logo" style="height: 30px; width: 30px; border-radius: 50%;">
      <span class="navbar-brand-text">Velnor</span>
    </a>
    
    <div class="dropdown" style="position: absolute; right: 15px;">
      <a class="nav-link dropdown-toggle d-flex align-items-center p-0" href="#" id="profileDropdown"
          role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="true"
          aria-label="User menu" style="cursor: pointer;">
          <div style="
              width: 32px;
              height: 32px;
              border-radius: 50%;
              background-color: #ff6600;
              color: white;
              display: flex;
              justify-content: center;
              align-items: center;
              font-weight: bold;
              font-size: 14px;
              user-select: none;">
              <?php echo htmlspecialchars($userInitials); ?>
          </div>
      </a>
      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
          <li><a class="dropdown-item" href="purchase history.php">Purchase History</a></li>
                          <li><a class="dropdown-item" href="submit feedback.php">Send Us feedback</a></li>

          <li><a class="dropdown-item" href="logout.php">Logout</a></li>
      </ul>
    </div>
    
  </div>
</nav>
