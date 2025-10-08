<?php
require_once __DIR__.'/../includes/auth.php';
require_role('secretaria');
include __DIR__.'/../templates/header.php';
?>
<style>
.hero {
  background:
    radial-gradient(1200px 200px at 100% 0, rgba(255,255,255,.15), transparent 60%),
    linear-gradient(135deg,#0d6efd 0%,#0aa2c0 100%);
  color:#fff; border-radius:20px; padding:28px;
  display:flex; gap:22px; align-items:center; box-shadow:0 10px 30px rgba(13,110,253,.25);
}
.hero-logo { width:96px; height:96px; border-radius:50%; background:#fff; padding:10px;
  box-shadow:0 8px 24px rgba(0,0,0,.25); object-fit:contain;
}
.hero-title { font-size:1.9rem; font-weight:800; letter-spacing:.2px; margin:0 }
.hero-sub   { opacity:.95; margin:2px 0 0 }

.info-card { background:#fff; border-radius:16px; padding:22px;
  box-shadow:0 6px 18px rgba(0,0,0,.08); height:100%;
}
.info-card h4 { font-weight:800; margin-bottom:10px }
.icon-pill { display:inline-flex; align-items:center; justify-content:center;
  width:36px; height:36px; border-radius:12px; background:#e7f1ff; color:#0d6efd; margin-right:8px;
}
</style>

<div class="hero">
  <picture>
    <source srcset="img/logo.webp" type="image/webp">
    <source srcset="img/logo.png"  type="image/png">
    <img src="img/logo.jpg" class="hero-logo" alt="Logo" onerror="this.style.display='none'">
  </picture>
  <div>
    <h1 class="hero-title">Tu salud es nuestro compromiso</h1>
    <p class="hero-sub">Centro de Salud Sur — Huehuetenango</p>
  </div>
</div>

<div class="row g-3 mt-4">
  <div class="col-lg-6">
    <div class="info-card">
      <h4><span class="icon-pill"><i class="bi bi-flag"></i></span>Misión</h4>
      <p class="text-muted mb-0">[Pendiente de definir.]</p>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="info-card">
      <h4><span class="icon-pill"><i class="bi bi-eye"></i></span>Visión</h4>
      <p class="text-muted mb-0">[Pendiente de definir.]</p>
    </div>
  </div>
</div>

<?php include __DIR__.'/../templates/footer.php'; ?>
