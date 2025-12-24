<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">    
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/remixicon@4.1.0/fonts/remixicon.css" rel="stylesheet"/>
        <link rel="stylesheet" href="style.css">
        <title>Rumah Makan Sederhana</title>
    </head>

    <body>
        <nav>
            <div class="nav__header">
                <div class="logo nav__logo">
                    <a href="index.php">RM <span>Sederhana</span></a>
                </div>
                <div class="nav__menu__btn" id="menu-btn" role="button" tabindex="0" aria-label="Buka menu" aria-controls="nav-link" aria-expanded="false">
                  <span><i class="ri-menu-line"></i></span>
                </div>
            </div>

            <ul class="nav__links" id="nav-link">
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact Us</a></li>
            </ul>
            
            <!-- Added profile button to navigation -->
            <div class="nav__btn">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php" class="btn" title="Profil Saya">
                        <i class="ri-user-line"></i>
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn">
                        <i class="ri-login-box-line"></i> Login
                    </a>
                <?php endif; ?>
            </div>
        </nav>

        <section class="section__container header__container" id="home">
            <div class="header__image" id="">
                <img src="image/rumahmakan.jpg" alt="rumah makan">
            </div>
            
            <div class="header__content">
                <h1>Nikmati rasa hidangan dengan <span>suasana nyaman!</span></h1>
                <p class="section__description">
                    Rumah Makan Sederhana menyediakan berbagai macam hidangan lezat yang siap disantap. Nikmati makanan kami dengan suasana nyaman dan pelayanan yang ramah.
                </p>
                <div class="header__btn">
                    <ul> 
                        <li><a href="menu.php"><button class="btn">Lihat Menu</button></a></li>
                    </ul>
                </div>
            </div>
        </section>

        <footer>
          <p>&copy; 2025 Rumah Makan Sederhana. Semua hak dilindungi.</p>
        </footer>

        <script src="script.js" defer></script>
    </body>
</html>
