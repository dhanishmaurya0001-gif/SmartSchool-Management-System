"""Smoke-check the bundled fictional dataset after starting the application.

Python standard library only. Does not change records or send messages.
"""
from urllib.request import build_opener, HTTPCookieProcessor, Request
from urllib.parse import urlencode, urlparse
from http.cookiejar import CookieJar
from html.parser import HTMLParser
import sys,json

class ChildChoices(HTMLParser):
    def __init__(self):
        super().__init__(); self.values = []
    def handle_starttag(self, tag, attrs):
        values = dict(attrs)
        if tag == 'input' and values.get('name') == 'clschg':
            self.values.append(values.get('value'))

base = (sys.argv[1] if len(sys.argv) > 1 else 'http://127.0.0.1:18097').rstrip('/')
if urlparse(base).hostname not in ('localhost', '127.0.0.1', '::1'):
    raise SystemExit('This fixture test accepts localhost only. It is not a production test.')

cases = [
    ('Super Admin', 'demo.superadmin@pragati.example', True, 'student/search'),
    ('Admin', 'admin@pragati.example', True, 'admin/approve_leave'),
    ('Teacher', 'teacher@pragati.example', True, 'homework'),
    ('Accountant', 'accountant@pragati.example', True, 'studentfee'),
    ('Librarian', 'librarian@pragati.example', True, 'admin/book/getall'),
    ('Receptionist', 'reception@pragati.example', True, 'admin/enquiry'),
    ('Student', 'student.004', False, 'user/homework'),
    ('Parent', 'parent.001', False, 'user/user/getfees'),
]
count = 0
for role, username, staff, page in cases:
    client = build_opener(HTTPCookieProcessor(CookieJar()))
    login_path = '/site/login' if staff else '/site/userlogin'
    initial = client.open(base + login_path, timeout=30)
    initial.read()
    actual_login = initial.url
    ajax = '/gauthenticate/' in actual_login
    if ajax:
        actual_login = base + ('/gauthenticate/verfiy_login' if staff else '/gauthenticate/verfiy_userlogin')
    response = client.open(Request(actual_login, data=urlencode({
        'username': username, 'password': 'Demo@School2026!'
    }).encode()), timeout=60)
    body = response.read()
    if ajax:
        result = json.loads(body)
        assert result.get('status') == 2 and not result.get('authenticator'), role + ': expected unenrolled demo login'
        response = client.open(result['redirect_to'], timeout=60)
        body = response.read()
    if '/choose' in response.url:
        choices = ChildChoices(); choices.feed(body.decode())
        assert choices.values, role + ': no child/session choices'
        response = client.open(Request(response.url,data=urlencode({'clschg':choices.values[0]}).encode()),timeout=60)
        response.read()
    assert '/dashboard' in response.url, f'{role}: login did not reach dashboard'
    count += 1
    response = client.open(base + '/' + page, timeout=60)
    body = response.read().decode(errors='replace')
    assert response.status == 200 and 'unauthorized' not in response.url
    assert 'A Database Error Occurred' not in body and 'A PHP Error was encountered' not in body
    count += 1
    print(role + ': login and representative page PASS')
    if role == 'Super Admin':
        for template, exam, group, label in [('1001','1001','1001','Half Yearly'),('1002','1003','1002','Photo Progress Card')]:
            payload = {'marksheet_template':template,'post_exam_id':exam,'post_exam_group_id':group,
                       'student_id':'1001','exam_group_class_batch_exam_student_id':'1001','type':'download'}
            pdf = client.open(Request(base + '/admin/examresult/pdftmarksheet',data=urlencode(payload).encode()),timeout=90).read()
            assert pdf.startswith(b'%PDF'), label + ': PDF output failed'
            count += 1
            print(label + ': PDF signature PASS')
print(str(count) + ' smoke checks passed. These are not exhaustive workflow tests.')
