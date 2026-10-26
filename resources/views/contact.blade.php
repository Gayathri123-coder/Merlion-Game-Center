<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us - Merlion Game Station</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
  :root {
    --bg:#0b0f19;
    --card:#131826;
    --text:#e2e8f0;
    --muted:#94a3b8;
    --brand:#8b5cf6;
    --brand2:#06b6d4;
  }
  body {
    margin:0;
    font-family:"Poppins", sans-serif;
    background:radial-gradient(circle at top right,rgba(124,58,237,0.2),transparent),
               radial-gradient(circle at bottom left,rgba(6,182,212,0.15),transparent),
               var(--bg);
    color:var(--text);
  }
  .container{
    max-width:900px;
    margin:auto;
    padding:40px 20px;
  }
  h1{
    text-align:center;
    font-size:34px;
    margin-bottom:28px;
    font-weight:700;
    background:linear-gradient(90deg,var(--brand),var(--brand2));
    -webkit-background-clip:text;
    color:transparent;
  }
  .contact-card{
    background:var(--card);
    border-radius:16px;
    padding:28px;
    box-shadow:0 15px 30px rgba(0,0,0,0.35);
  }
  .row{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:20px;
  }
  @media(max-width:700px){
    .row{grid-template-columns:1fr;}
  }
  .item{
    background:rgba(255,255,255,0.04);
    border:1px solid rgba(255,255,255,0.08);
    padding:18px;
    border-radius:12px;
    line-height:1.5;
  }
  .item strong{
    display:block;
    margin-bottom:10px;
    color:var(--brand2);
    font-size:18px;
  }
  a{
    color:var(--text);
    text-decoration:none;
    display:flex;
    align-items:center;
    gap:8px;
    margin-bottom:6px;
    font-weight:500;
  }
  a:hover{ color:var(--brand2); }
  .icon{ color:var(--brand2); font-size:17px; }
  .social-icons{
    display:flex;
    gap:15px;
    margin-top:5px;
  }
  .social-icons a{
    width:38px;
    height:38px;
    display:flex;
    justify-content:center;
    align-items:center;
    border-radius:50%;
    background:rgba(255,255,255,0.05);
    border:1px solid rgba(255,255,255,0.1);
    color:var(--brand2);
    font-size:18px;
    transition:0.3s;
  }
  .social-icons a:hover{
    background:var(--brand2);
    color:#0b0f19;
    transform:translateY(-3px);
    box-shadow:0px 4px 12px rgba(6,182,212,0.4);
  }
  .map-wrapper{
    margin-top:25px;
    border-radius:12px;
    overflow:hidden;
    border:1px solid rgba(255,255,255,0.1);
  }
  iframe{
    width:100%;
    height:350px;
    border:none;
  }
  footer{
    text-align:center;
    margin-top:20px;
    color:var(--muted);
    font-size:14px;
  }
</style>
</head>
<body>

<div class="container">
  <h1>Contact Us</h1>

  <div class="contact-card">

    <!-- ✅ First Row -->
    <div class="row">
      <div class="item">
        <strong>Contact</strong>
        <a href="tel:+919585069933"><i class="fa-solid fa-phone icon"></i>+91 95850 69933</a>
        <a href="https://wa.me/919585069933" target="_blank"><i class="fa-brands fa-whatsapp icon"></i>Chat on WhatsApp</a>
      </div>
      <div class="item">
        <strong>Email</strong>
        <a href="mailto:merliongame@gmail.com"><i class="fa-solid fa-envelope icon"></i>merliongame@gmail.com</a>
      </div>
    </div>

    <!-- ✅ Second Row -->
    <div class="row" style="margin-top:20px;">
      <div class="item">
        <strong>Address</strong>
        <a href="https://maps.app.goo.gl/6Nm3SQUwBBCWp1haA?g_st=ac" target="_blank" style="display:block;">
          Mettur Main Road,<br>Near Mettur Railway Gate,<br>Omalur, Salem – 636455
        </a>
      </div>
      <div class="item">
        <strong>Follow Us</strong>
        <div class="social-icons">
          <a href="https://www.facebook.com/share/1CSHjKACHW/" target="_blank"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="https://www.instagram.com/merlion_game_station?igsh=MTNvdXRhdGJ6eGFjNw==" target="_blank"><i class="fa-brands fa-instagram"></i></a>
        </div>
      </div>
    </div>

    <!-- ✅ Full Width Map -->
    <div class="map-wrapper">
      <a href="https://www.google.com/maps/dir/?api=1&destination=Mettur+Main+Road,+Near+Mettur+Railway+Gate,+Omalur,+Salem+636455" target="_blank">
        <iframe src="https://www.google.com/maps?q=Mettur%20Main%20Road,%20Near%20Mettur%20Railway%20Gate,%20Omalur,%20Salem%20636455&output=embed"></iframe>
      </a>
    </div>

  </div>

  <footer>
    © <span id="year"></span> Merlion Game Station • All rights reserved.
  </footer>
</div>

<script>
  document.getElementById("year").textContent = new Date().getFullYear();
</script>

</body>
</html>
