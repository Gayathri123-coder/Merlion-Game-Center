<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Privacy Policy - Merlion Game Station</title>
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
      margin-bottom:28px;
      font-weight:700;
      background:linear-gradient(90deg,var(--brand),var(--brand2));
      -webkit-background-clip:text;
      color:transparent;
    }
    .policy-card{
      background:var(--card);
      border-radius:16px;
      padding:28px;
      box-shadow:0 15px 30px rgba(0,0,0,0.35);
      line-height:1.7;
    }
    h2{
      font-size:20px;
      margin-top:20px;
      color:var(--brand2);
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
      margin-top:30px;
      font-size:14px;
      color:var(--muted);
    }
  </style>
</head>
<body>

  <div class="container">
    <h1>Privacy Policy</h1>

    <div class="policy-card">

      <p>Your privacy is important to us. This Privacy Policy explains how Merlion Game Station handles your personal information.</p>

      <h2>📌 1. Information We Collect</h2>
      <ul>
        <li>✔ Name, phone number, and email address when you contact or book with us.</li>
        <li>✔ Booking details such as date, time slot, and service selected.</li>
        <li>✔ Payment information (processed securely through Razorpay – we do not store card details).</li>
        <li>✔ Optional messages or feedback shared by you.</li>
      </ul>

      <h2>📌 2. How We Use Your Information</h2>
      <ul>
        <li>✔ To confirm bookings and contact you regarding your slot.</li>
        <li>✔ To provide customer support and respond to inquiries.</li>
        <li>✔ To improve our services and gaming experience.</li>
        <li>✔ For security and safety of all customers and property.</li>
      </ul>

      <h2>📌 3. Data Sharing & Security</h2>
      <ul>
        <li>✔ We do <strong>not</strong> sell, rent, or share your personal details with third parties.</li>
        <li>✔ Payments are processed securely through <strong>Razorpay</strong> using encryption and PCI-DSS standards.</li>
        <li>✔ We may share information only if required by law or for safety reasons.</li>
      </ul>

      <h2>📌 4. Your Rights</h2>
      <ul>
        <li>✔ You may request correction or deletion of your personal data.</li>
        <li>✔ You can contact us anytime for privacy-related concerns.</li>
      </ul>

      <h2>📌 5. Contact Us</h2>
      <p>
        📞 +91 95850 69933 <br>
        ✉️ merliongame@gmail.com <br>
        📍 Mettur Main Road, Near Mettur Railway Gate, Omalur, Salem – 636455
      </p>

      <h2>📌 6. Updates to This Policy</h2>
      <p>We may update this Privacy Policy from time to time. Continued use of our services means you accept the updated policy.</p>

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
