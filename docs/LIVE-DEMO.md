# Live interview demo

The fictional school demonstration is deployed on **Render Free**.

- **Staff / Super Admin:** https://smart-school-interview-demo.onrender.com/site/login
- **Student / Parent:** https://smart-school-interview-demo.onrender.com/site/userlogin

All accounts below intentionally use **`Demo@School2026!`**. They access only the disposable fictional demonstration. Log out before changing roles, or use separate browser profiles.

| Role | Username | Useful first operation |
|---|---|---|
| Super Admin | `demo.superadmin@pragati.example` | Student Details → Class 6 / A; examinations and progress cards |
| Admin | `admin@pragati.example` | Admission review, timetable and pending leave approval |
| Teacher | `teacher@pragati.example` | Class 6-A attendance, homework and lesson progress |
| Accountant | `accountant@pragati.example` | Mixed paid/partial/unpaid fees, receipts and bank requests |
| Librarian | `librarian@pragati.example` | Book issue, return and member history |
| Receptionist | `reception@pragati.example` | Enquiry follow-up, visitors, calls and complaints |
| Student | `student.004` | Pending homework, practice exam and enrolled courses |
| Parent | `parent.001` | Sibling selection, fees, attendance and homework |

The dataset includes 80 students, 40 parent accounts and 11 staff for session 2026–27. Student `student.001` is Aarav Sharma, Class 6-A, with evaluated homework and sample results. Parent `parent.001` has children in Class 6-A and Class 8-B.

## Suggested interview sequence

1. Super Admin: dashboard, Class 6-A students, profile and timetable.
2. Teacher: homework/attendance; Student: submission/status and online examination.
3. Parent: child selection, attendance and fees.
4. Accountant: a student's outstanding installment, cash collection, receipt and reports.
5. Librarian: book availability, issue and return.
6. Receptionist: admission enquiry follow-up and visitor entry.
7. Admin: leave approval; Print Marksheet with the normal Half Yearly or photo-style combined progress-card template.

## Free-hosting behavior

Render Free sleeps after inactivity, so the first opening may take about a minute. Open the link before the interview starts. The application and its local MariaDB run in one isolated container; no separately billed database or persistent disk is configured.

**Changes and uploaded files can reset when the free container restarts, redeploys or wakes in a fresh instance.** The included fictional dataset is automatically reloaded. This is a resettable interview environment, not durable school hosting.

Public demo visitors can use school business workflows. Deployment-only rules deny source/add-on installation, updates, SQL backup/restore and remote multi-branch connection administration. The original owner account is disabled in this hosted demo; the listed Super Admin is a deliberate demo account. The local source distribution keeps its separate setup behavior.

## Hosted verification — 21 November 2026

- **26/26 smoke checks:** eight role logins, two representative read-only pages per role and two PDF routes.
- Normal Half Yearly marksheet and custom photo-style progress-card PDFs generated successfully.
- **Librarian:** create temporary book → issue → return → verify availability → remove test book/history.
- **Receptionist:** create temporary visitor → view → remove test visitor.
- **Accountant:** collect one fictional currency unit → generate receipt → revert only the new test payment, preserving the seeded payments.
- Temporary business records were cleaned up; ordinary login/audit logs remain.

These are focused hosted checks, not a claim that every submenu or external integration has been exhaustively accepted. Real payment settlement, Zoom/Google Meet meetings, WhatsApp delivery and physical scanner/printer output require their own configuration/hardware and are not represented as live-tested here.

Official hosting limits: https://render.com/docs/free
