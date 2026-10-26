<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cancellation & Refund Policy - Merlion Game Station</title>
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
    .container {
      max-width:900px;
      margin:auto;
      padding:40px 20px;
    }
    h1 {
      text-align:center;
      font-size:34px;
      margin-bottom:28px;
      font-weight:700;
      background:linear-gradient(90deg,var(--brand),var(--brand2));
      -webkit-background-clip:text;
      color:transparent;
    }
    .policy-card {
      background:var(--card);
      border-radius:16px;
      padding:28px;
      box-shadow:0 15px 30px rgba(0,0,0,0.35);
      line-height:1.7;
    }
    h2 {
      color:var(--brand2);
      margin-top:20px;
      font-size:20px;
    }
    p, li {
      color:var(--muted);
      font-size:15px;
    }
    ul {
      margin-left:20px;
    }
    footer {
      text-align:center;
      margin-top:30px;
      color:var(--muted);
      font-size:14px;
    }
  </style>
</head>
<body>

  <div class="container">
    <h1>Cancellation & Refund Policy</h1>

    <div class="policy-card">

      <p>At Merlion Game Station, we always aim to provide the best gaming experience. However, in the case of cancellations or refunds, the following rules will apply:</p>

      <h2>📌 Refund Conditions</h2>
      <ul>
        <li>✔ Refunds are available **only at the shop (offline)**.</li>
        <li>❌ No refunds will be processed through **phone, WhatsApp, Instagram, or online request**.</li>
        <li>✔ If a booking is cancelled **before the gaming slot starts**, refund or reschedule may be allowed at management's discretion.</li>
        <li>❌ If the customer is late and the time slot is already running or over, **no refund or extra time will be provided**.</li>
      </ul>

      <h2>📌 Advance Booking & Reschedule Policy</h2>
      <ul>
        <li>✔ Slot can be rescheduled once (subject to availability).</li>
        <li>✔ Reschedule requests must be made **before 2 hours of booking time**.</li>
        <li>❌ Reschedule is not allowed after the booking time has started.</li>
      </ul>

      <h2>📌 Damage & Misuse Charges</h2>
      <ul>
        <li>💥 Any damage caused to console, controllers, headset, or accessories must be paid by the user.</li>
        <li>🔧 Repair/replacement cost will be charged immediately at the venue.</li>
      </ul>

      <h2>📌 Final Note</h2>
      <p><strong>By booking or using our services, you accept the above Cancellation & Refund Policy.</strong></p>

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
