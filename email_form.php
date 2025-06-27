<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="send_mail.php" method="post">
  <input type="text" name="receipt_name" placeholder="Your Name">
  <input type="email" name="receipt_email" placeholder="Your Email">
  <input type='text' name="subject" placeholder="subject">
  <textarea name="message" placeholder="Message"></textarea>
  <button type="submit">Send</button>
</form>

</body>
</html>