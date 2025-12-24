<!DOCTYPE html>
<html lang="id">    
  <head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.1.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="style.css"/>
    <title>Hubungi Kami - Rumah Makan Sederhana</title>
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
        <li><a href="contact.php" aria-current="page">Contact Us</a></li>
      </ul>
    </nav>

    <main>
      <section class="section__container header__container" id="contact">
        <div class="contact__layout">
          <div class="contact__main">
            <div class="header__content">
              <h1>Hubungi <span>Kami</span></h1>
              <p class="section__description">
                Punya pertanyaan, kritik, atau saran? Silakan sampaikan melalui formulir di bawah ini.
              </p>

              <form id="contact-form" class="contact__form">
                <label class="field">
                  <span>Nama</span>
                  <input type="text" name="nama" required placeholder="Nama lengkap"/>
                </label>
                <label class="field">
                  <span>Email</span>
                  <input type="email" name="email" required placeholder="Email Anda"/>
                </label>
                <label class="field">
                  <span>Pesan</span>
                  <textarea name="pesan" rows="5" required placeholder="Tulis pesan Anda di sini..."></textarea>
                </label>
                <div>
                  <button type="submit" class="btn">Kirim Pesan</button>
                </div>
              </form>
              
              <div id="formFeedback" aria-live="polite"></div>

              <br><br>
              <div class="contact-info">
                <h3>Informasi Kontak & Koneksi</h3>
                
                <div class="info-item">
                  <i class="ri-mail-line"></i>
                  <div>
                    <strong>Email</strong>
                    <p>rumahmakansederhana@gmail.com</p>
                    <a href="mailto:rumahmakansederhana@gmail.com" class="btn btn-contact">
                      <i class="ri-mail-send-line"></i> Kirim Email
                    </a>
                  </div>
                </div>

                <div class="info-item">
                  <i class="ri-phone-line"></i>
                  <div>
                    <strong>WhatsApp</strong>
                    <p>+62 895-3806-76050</p>
                    <a href="https://wa.me/62895380676050" target="_blank" rel="noopener noreferrer" class="btn btn-contact">
                      <i class="ri-whatsapp-line"></i> Chat WhatsApp
                    </a>
                  </div>
                </div>

                <div class="info-item">
                  <i class="ri-map-pin-line"></i>
                  <div>
                    <strong>Lokasi</strong>
                    <p>Jl. Alamsyah Ratu Prawiranegara No.119, Bukit Lama, Kec. Ilir Bar. I, Palembang 30138</p>
                    <a href="https://maps.app.goo.gl/3oEy7QWe7E8L7hoRA" target="_blank" rel="noopener noreferrer" class="btn btn-contact">
                      <i class="ri-map-2-line"></i> Buka Google Maps
                    </a>
                  </div>
                </div>
                        
                <div class="info-item">
                  <i class="ri-instagram-line"></i> 
                  <div>
                    <strong>Media Sosial</strong>
                    <p>Instagram RM.Sederhana</p>
                    <a href="https://www.instagram.com/rumah_makan_sederhana_poligon?igsh=Y2NwaTA1ZTB6czhr" target="_blank" rel="noopener noreferrer" class="social-link">
                      <i class="ri-instagram-2-line"></i> Buka Instagram
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>
    <br><br>
    
    <footer>
      <p>&copy; 2025 Rumah Makan Sederhana. Semua hak dilindungi.</p>
    </footer>

    <script src="script.js" defer></script>
    <!-- Added contact form submission handler -->
    <script>
      console.log('[v0] Contact page loaded');
      
      document.getElementById('contact-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        console.log('[v0] Form submitted');
        
        const formData = new FormData(e.target);
        const feedback = document.getElementById('formFeedback');
        
        // Show loading state
        feedback.innerHTML = '<p style="color: #2196F3; padding: 1rem; background: #e3f2fd; border-radius: 0.5rem; margin-top: 1rem;">Mengirim pesan...</p>';
        
        try {
          console.log('[v0] Sending request to process_contact.php');
          const response = await fetch('process_contact.php', {
            method: 'POST',
            body: formData
          });
          
          console.log('[v0] Response received:', response.status);
          const result = await response.json();
          console.log('[v0] Result:', result);
          
          if (result.success) {
            feedback.innerHTML = `<p style="color: #4CAF50; padding: 1rem; background: #e8f5e9; border-radius: 0.5rem; margin-top: 1rem;">${result.message}</p>`;
            e.target.reset();
          } else {
            feedback.innerHTML = `<p style="color: #f44336; padding: 1rem; background: #ffebee; border-radius: 0.5rem; margin-top: 1rem;">${result.message}</p>`;
          }
        } catch (error) {
          console.error('[v0] Error:', error);
          feedback.innerHTML = `<p style="color: #f44336; padding: 1rem; background: #ffebee; border-radius: 0.5rem; margin-top: 1rem;">Terjadi kesalahan: ${error.message}</p>`;
        }
      });
    </script>
  </body>
</html>