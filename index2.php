<?php
/**
 * Markezo Theme Template
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="description" content="Markezo: ten local shops in one online bazaar. Browse each store, see products and location, and message the shop on WhatsApp.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=DM+Sans:wght@400;500;700&display=swap">
<link rel="stylesheet" href="<?php echo esc_url( get_stylesheet_uri() ); ?>">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="bar"><div class="wrap">
  <a class="logo" href="<?php echo esc_url( home_url( '#home' ) ); ?>" aria-label="Markezo home"><span class="mk" id="logoIc"></span>Markezo</a>
  <div class="tools">
    <button class="ibtn" id="themeBtn" type="button" aria-label="Switch light or dark theme">☀</button>
    <button class="cartbtn" id="cartBtn" type="button" aria-label="Open request list"><span class="t">Request list</span><b id="cartCount">0</b></button>
  </div>
</div></div>

<main class="wrap" id="view">
  <?php
  if ( have_posts() ) :
    echo '<div class="grid">';
    while ( have_posts() ) :
      the_post();
      ?>
      <article class="card">
        <h2><?php the_title(); ?></h2>
        <div><?php the_excerpt(); ?></div>
        <button class="btn" type="button" data-add-title="<?php echo esc_attr( get_the_title() ); ?>">Add to request list</button>
      </article>
      <?php
    endwhile;
    echo '</div>';
  endif;
  ?>
</main>

<footer><div class="wrap"><div><b>Markezo</b><p>Ten local shops, one online bazaar. Each shop confirms its prices when you message it.</p></div></div></footer>

<button class="fab" id="fab" type="button" hidden>View request list <b id="fabCount">0</b></button>
<div class="toast" id="toast" role="status" aria-live="polite" hidden></div>
<div id="veil" class="veil" hidden></div>
<aside id="panel" class="panel" hidden aria-label="Request list">
  <header><h2>Request list</h2><button class="ibtn" id="closeCart" type="button" aria-label="Close">✕</button></header>
  <p class="hint">Tap Send on WhatsApp to open a chat with the shop. Your request is already typed in the message.</p>
  <div class="list2" id="cartBody"></div>
</aside>

<script>
(function () {
  const panel = document.getElementById("panel");
  const veil = document.getElementById("veil");
  const cartBody = document.getElementById("cartBody");
  const cartCount = document.getElementById("cartCount");
  const fab = document.getElementById("fab");
  const fabCount = document.getElementById("fabCount");
  const toast = document.getElementById("toast");
  const themeBtn = document.getElementById("themeBtn");
  let list = JSON.parse(localStorage.getItem("markezo_wp_req") || "[]");
  let theme = localStorage.getItem("markezo_wp_theme") || "light";

  function applyTheme() {
    document.documentElement.setAttribute("data-theme", theme);
    themeBtn.textContent = theme === "dark" ? "☾" : "☀";
    localStorage.setItem("markezo_wp_theme", theme);
  }
  function notify(msg) {
    toast.textContent = msg;
    toast.hidden = false;
    clearTimeout(notify._t);
    notify._t = setTimeout(() => { toast.hidden = true; }, 2000);
  }
  function render() {
    cartCount.textContent = list.length;
    fabCount.textContent = list.length;
    fab.hidden = list.length === 0 || !panel.hidden;
    if (!list.length) {
      cartBody.innerHTML = '<p class="hint">Your request list is empty.</p>';
      return;
    }
    const text = encodeURIComponent("Assalam-o-Alaikum, I'd like to check:\n" + list.map((x, i) => `${i + 1}. ${x}`).join("\n"));
    cartBody.innerHTML = `
      ${list.map((x, i) => `<div><span>${x}</span> <button class="btn" type="button" data-del="${i}">Remove</button></div>`).join("")}
      <a class="btn" style="background:var(--accent);color:var(--accent-ink);text-decoration:none;margin-top:8px;" href="https://wa.me/?text=${text}" target="_blank" rel="noopener">Send on WhatsApp</a>
    `;
    cartBody.querySelectorAll("[data-del]").forEach(b => b.addEventListener("click", () => {
      list.splice(Number(b.dataset.del), 1);
      localStorage.setItem("markezo_wp_req", JSON.stringify(list));
      render();
    }));
  }
  document.querySelectorAll("[data-add-title]").forEach(btn => btn.addEventListener("click", () => {
    list.push(btn.dataset.addTitle);
    localStorage.setItem("markezo_wp_req", JSON.stringify(list));
    notify(`Added "${btn.dataset.addTitle}"`);
    render();
  }));
  themeBtn.addEventListener("click", () => { theme = theme === "dark" ? "light" : "dark"; applyTheme(); });
  document.getElementById("cartBtn").addEventListener("click", () => { panel.hidden = false; veil.hidden = false; fab.hidden = true; });
  fab.addEventListener("click", () => { panel.hidden = false; veil.hidden = false; fab.hidden = true; });
  document.getElementById("closeCart").addEventListener("click", () => { panel.hidden = true; veil.hidden = true; render(); });
  veil.addEventListener("click", () => { panel.hidden = true; veil.hidden = true; render(); });
  applyTheme();
  render();
})();
</script>
<?php wp_footer(); ?>
</body>
</html>
