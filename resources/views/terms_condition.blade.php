<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Terms & Conditions - Merlion Game Station</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    :root{
      --bg:#0b0f19;
      --card:#131826;
      --text:#e2e8f0;
      --muted:#94a3b8;
      --brand:#8b5cf6;
      --brand2:#06b6d4;
    }
    body{
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
      font-weight:700;
      margin-bottom:28px;
      background:linear-gradient(90deg,var(--brand),var(--brand2));
      -webkit-background-clip:text;
      color:transparent;
    }
    .terms-card{
      background:var(--card);
      border-radius:16px;
      padding:28px;
      box-shadow:0 15px 30px rgba(0,0,0,0.35);
      line-height:1.7;
    }
    h2{
      color:var(--brand2);
      margin-top:25px;
      font-size:20px;
    }
    p, li{
      color:var(--muted);
      font-size:15px;
    }
    ul{
      margin-left:20px;
    }
    footer{
      text-align:center;
      margin-top:25px;
      font-size:14px;
      color:var(--muted);
    }
  </style>
</head>
<body>

<div class="container">
  <h1>Terms & Conditions</h1>

  <div class="terms-card">
    <p>By accessing, booking, or using any service at Merlion Game Station, you agree to follow the terms and conditions outlined below:</p>

    <h2>1. Booking & Payment</h2>
    <ul>
      <li>✔ Bookings must be confirmed in advance or at the counter.</li>
      <li>✔ An advance or full payment may be required to confirm a booking.</li>
      <li>✔ If you arrive late, the slot time will not be extended or refunded.</li>
    </ul>

   

    <h2>2. Equipment Use & Damage</h2>
    <ul>
      <li>✔ All gaming equipment must be used responsibly and handled with care.</li>
      <li>💥 Any damage to gaming consoles, controllers, headsets, or accessories caused by the user will be charged.</li>
    </ul>

    <h2>3. Food, Drinks & Cleanliness</h2>
    <ul>
      <li>❌ Outside food or beverages are not permitted near gaming areas to protect equipment.</li>
      <li>✔ Food or drinks purchased inside the shop must be placed in the assigned area only.</li>
    </ul>

    <h2>4. Behaviour & Conduct</h2>
    <ul>
      <li>✔ Respectful behaviour towards staff and other customers is expected at all times.</li>
      <li>❌ Abusive language, harassment, intoxication, or misconduct is strictly prohibited.</li>
      <li>✔ Management reserves the right to stop a session or refuse entry without refund in case of misbehaviour.</li>
    </ul>

    <h2>5. Personal Belongings</h2>
    <ul>
      <li>✔ Customers are responsible for their own personal belongings.</li>
      <li>❌ Merlion Game Station is not responsible for loss or theft of valuables.</li>
    </ul>

    
  <footer>
    © <span id="year"></span> Merlion Game Station • All rights reserved.
  </footer>
</div>

<script>
  document.getElementById("year").textContent = new Date().getFullYear();
</script>

</body>
</html>
