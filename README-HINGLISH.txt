SCHOOL INTERVIEW DEMO - DOCKER DEPLOYMENT SCAFFOLD

STATUS
Yeh deployment scaffold prepare kiya gaya hai. Is PC par Docker engine build/run
verification abhi nahi hui hai. Is file ko successful cloud deployment ka proof na samjhein.

REQUIRED PACKAGE CONTENTS
1. Is directory ke andar app/ mein authorized full Smart School application source.
2. database.sql mein fictional interview demo database snapshot.
3. App configuration ko DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASSWORD,
   APP_URL aur SMART_SCHOOL_APP_KEY environment variables se read karna hoga.
   App config changes packager karega; yeh scaffold akela app configuration change nahi karta.

LOCAL START
1. Docker Desktop / Docker Engine with Docker Compose install aur start karein.
2. Complete package ko short folder mein extract karein, jaise C:\SchoolInterview.
3. .env.example ki copy .env banayein.
4. DB_PASSWORD, DB_ROOT_PASSWORD aur SMART_SCHOOL_APP_KEY ke liye teen ALAG
   random values bharein. `openssl rand -hex 32` har value ke liye use kar sakte hain.
   Secret values, .env aur REAL school database backups public GitHub par upload
   na karein. Authorized fictional demo database.sql is package ka part ho sakta hai.
   Windows PowerShell mein random value banane ka alternative:
      $bytes = New-Object byte[] 32
      [Security.Cryptography.RandomNumberGenerator]::Create().GetBytes($bytes)
      -join ($bytes | ForEach-Object { $_.ToString('x2') })
   Is generator ko teen baar run karein. Output ko sirf local .env mein paste karein.
5. Is folder mein terminal kholkar run karein:
      docker compose up --build -d
6. Status dekhein:
      docker compose ps
      docker compose logs --tail=100 app db
7. Jab app healthy ho, kholen:
      http://127.0.0.1:8080/site/login
   Student/Parent login: http://127.0.0.1:8080/site/userlogin
8. Demo role logins ka separate private guide use karein.

VS CODE
VS Code -> File -> Open Folder -> complete extracted package select karein.
Terminal -> New Terminal se upar wale docker compose commands run karein.
PHP source app/ mein hai; configuration changes ke baad image rebuild karein.
PHP ko Live Server extension se run nahi kiya ja sakta; PHP + database runtime chahiye.
Full package mein original Windows XAMPP launcher diya ho to uske apne README
ke ports/credentials follow karein. Docker aur XAMPP do alag runtime methods hain.

STOP / RESTART / DATA
Stop: docker compose stop
Restart: docker compose start
Remove containers, keep records: docker compose down
Database aur uploaded files named Docker volumes mein preserve hote hain.
Initial database.sql sirf EMPTY database volume par first startup mein import hota hai.
SQL file replace karne se existing database dobara import nahi hota.
Normal operation ke liye `docker compose down -v` use NA karein: -v volumes/data hata deta hai.
Upload volume image ke uploads/ contents se first creation par populate hota hai.
Rebuild par naye image upload assets existing volume mein automatic merge nahi hote.
Database AND upload volume ka backup saath rakhein.

CLOUD HOSTING
PHP/Apache container aur private MySQL/MariaDB database support chahiye.
Cloud provider mein DB_* aur SMART_SCHOOL_APP_KEY secrets configure karein;
APP_URL ko provider ke actual https:// domain par set karein.
Provided Compose binding sirf 127.0.0.1 par hai; isse seedha public service nahi banti.
Public deployment ke liye provider ingress/HTTPS, persistent storage, backup aur
proper app reverse-proxy/secure-cookie configuration separately verify karna hoga.
Container Apache port 80 use karta hai; provider ko same internal port configure karein.
Cloud provider disk/volume paths, network DNS aur database port uske hisaab se set karein.

BOUNDARIES AND TESTS STILL REQUIRED
Yeh app login ke saath read/write fictional interview demonstration ke liye hai.
Public demo database ko real school data, real credentials ya production infrastructure
se kabhi connect na karein. Demo password publication ka decision separately karein.
Apache configuration private source/runtime paths ko deny karti hai aur uploads se
PHP/script execution deny karti hai; application-level authorization/CSRF review alag hai.
Build success, DB import, role logins, uploads, PDF, receipts and key workflows ko
container/provider environment mein verify karna baaki hai. Existing native tests
Docker/cloud tests ke equivalent nahi hain.
Zoom/Meet/WhatsApp/payment delivery aur physical QR/printer tests ke liye actual
external setup chahiye. Demo accounts se real messages or transactions na bhejein.

SOURCE RIGHTS
Base application aur paid add-ons third-party commercial source hain. Unhe sirf
authorized licensed package se provide/use karein. Yeh scaffold vendor source ko
open-source banane ya redistribute karne ki permission grant nahi karta.
