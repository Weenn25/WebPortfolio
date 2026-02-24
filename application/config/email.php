<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| Email Configuration
| 
| This file contains settings for the email library.
| Update the values below to use Gmail or another email service.
*/

// Gmail SMTP Configuration
$config['protocol'] = 'smtp';
$config['smtp_host'] = 'smtp.gmail.com';
$config['smtp_port'] = 587;
$config['smtp_user'] = 'erambonanzaa@gmail.com';  // Your Gmail address
$config['smtp_pass'] = 'ixoj jwzp mluj uuti';      // Your Gmail App Password (NOT your regular password)
$config['smtp_crypto'] = 'tls';
$config['mailtype'] = 'text';
$config['charset'] = 'utf-8';
$config['newline'] = "\r\n";
$config['crlf'] = "\r\n";

/*
| IMPORTANT: Create a Gmail App Password
| 
| 1. Go to https://myaccount.google.com/security
| 2. Enable 2-Step Verification (if not already enabled)
| 3. Go back to Security and find "App passwords"
| 4. Select "Mail" and "Windows Computer"
| 5. Copy the 16-character app password
| 6. Paste it in $config['smtp_pass'] above
| 
| DO NOT use your regular Gmail password!
*/
