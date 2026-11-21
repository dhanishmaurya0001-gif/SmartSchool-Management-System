from pathlib import Path
import secrets
root = Path(__file__).resolve().parent.parent
target = root / '.env'
if target.exists():
    raise SystemExit('.env already exists; kept unchanged.')
target.write_text('DB_NAME=school_demo\nDB_USER=school_app\nDB_PASSWORD=' + secrets.token_hex(24) + '\nDB_ROOT_PASSWORD=' + secrets.token_hex(32) + '\nSMART_SCHOOL_APP_KEY=' + secrets.token_hex(32) + '\nAPP_URL=http://127.0.0.1:8080/\n')
print('Created local .env with fresh secrets. Keep this file private.')
