<?php
// socialmedia.php — horizontal social icons with full brand colors

$socialMedia = [
  'facebook' => [
      'icon' => 'fab fa-facebook-f',
      'url'  => 'https://www.facebook.com/share/1Cch6CueRm/?mibextid=wwXIfr', // LInk of Geek Infotech
      'color'=> '#1877F2'
  ],
  'twitter' => [
      'icon' => 'fab fa-twitter',
      'url'  => 'https://twitter.com',
      'color'=> '#1DA1F2'
  ],
  'instagram' => [
      'icon' => 'fab fa-instagram',
      'url'  => 'https://www.instagram.com/rajiv_memorial_group?igsh=dHZycm5yZDVwbmd4', // link of Geek Infotech
      'color'=> 'linear-gradient(45deg, #F58529, #DD2A7B, #8134AF, #515BD4)'
  ],
  'linkedin' => [
      'icon' => 'fab fa-linkedin-in',
      'url'  => 'https://linkedin.com',
      'color'=> '#0A66C2'
  ],
  'youtube' => [
      'icon' => 'fab fa-youtube',
      'url'  => 'https://www.youtube.com',
      'color'=> '#FF0000'
  ],
  'whatsapp' => [
      'icon' => 'fab fa-whatsapp',
      'url'  => 'https://wa.me/917682810241', // replace with Principal number
      'color'=> '#25D366'
  ]
];
?>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Social Media Row -->
<div class="social-bar footer-style">
  <?php foreach ($socialMedia as $platform => $data): ?>
    <a href="<?php echo $data['url']; ?>" target="_blank" 
       class="icon <?php echo $platform; ?>" 
       title="<?php echo ucfirst($platform); ?>">
      <i class="<?php echo $data['icon']; ?>"></i>
    </a>
  <?php endforeach; ?>
</div>

<!-- Embedded CSS -->
<style>
/* 🌐 Universal Social Media Bar */
.social-bar {
  display: flex;
  flex-direction: row;
  justify-content: center;
  align-items: center;
  gap: 15px;
  flex-wrap: wrap;
  margin-top: 20px;
}

/* 🎨 Common Icon Style */
.social-bar .icon {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 45px;
  height: 45px;
  border-radius: 50%;
  color: #fff;
  font-size: 20px;
  text-decoration: none;
  transition: all 0.3s ease;
  box-shadow: 0 3px 6px rgba(0,0,0,0.4);
}

/* ✅ Native Brand Colors */
.icon.facebook { background-color: #1877F2; }
.icon.twitter { background-color: #1DA1F2; }
.icon.instagram {
  background: linear-gradient(45deg, #F58529, #DD2A7B, #8134AF, #515BD4);
}
.icon.linkedin { background-color: #0A66C2; }
.icon.youtube { background-color: #FF0000; }
.icon.whatsapp { background-color: #25D366; }

/* ✨ Hover Effect */
.social-bar .icon:hover {
  transform: scale(1.15);
  box-shadow: 0 0 15px rgba(255,255,255,0.5);
  filter: brightness(1.15);
}

/* 📱 Responsive */
@media (max-width: 600px) {
  .social-bar {
    gap: 10px;
  }
  .social-bar .icon {
    width: 40px;
    height: 40px;
    font-size: 18px;
  }
}
</style>
