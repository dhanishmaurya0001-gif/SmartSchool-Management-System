# Demo logins and interview sequence

These accounts belong to the fictional **Pragati Public School — Demo**, session **2026–27**. All listed demo accounts intentionally use **`Demo@School2026!`** for local testing. These credentials are public examples and must never be reused for a real school or exposed production system.

| Role | Username |
|---|---|
| Super Admin | `demo.superadmin@pragati.example` |
| Admin | `admin@pragati.example` |
| Teacher | `teacher@pragati.example` |
| Accountant | `accountant@pragati.example` |
| Librarian | `librarian@pragati.example` |
| Receptionist | `reception@pragati.example` |
| Student with pending homework | `student.004` |
| Parent with two children | `parent.001` |

Staff use `/site/login`; students/parents use `/site/userlogin`. The Windows launcher serves port **18097**; Docker serves port **8080**. Log out between roles or use separate browser profiles.

The Windows launcher generates a private initial password for `admin@smartschool.test` on first run. That owner password is not the shared demonstration password. Use the prepared demo Super Admin above for the interview sequence.

## Other useful records

- `student.001`: **Aarav Sharma**, Class 6-A, with evaluated homework and sample results.
- `student.004`: pending homework suitable for demonstrating a new submission.
- `student.002`: partial second-term fee example.
- `student.003`: partial/unpaid fee example suitable for collection and receipt demonstration.
- `parent.001`: siblings `student.001` (6-A) and `student.041` (8-B).
- Student accounts `.001`–`.078` are active examples; `.079` and `.080` are disabled examples.
- Bank references `DEMO-BANK-001` to `DEMO-BANK-006` are fictional pending requests. No real bank transfer occurred.

## A 30–40 minute demonstration

| Step | Role | Demonstration |
|---|---|---|
| 1 | Super Admin | Show dashboard, Class 6-A students, Aarav's profile, timetable and attendance history. |
| 2 | Receptionist | Open an admission enquiry, add a follow-up, record a visitor and review complaints. |
| 3 | Admin | Review a submitted online admission and demonstrate enrollment on a test record. |
| 4 | Teacher | Mark a new day's attendance, review submitted homework, evaluate it and apply for leave. |
| 5 | Student `.004` | Submit pending homework, inspect fee dues, open a course lesson or attempt the practice online exam. |
| 6 | Parent `.001` | Select/switch siblings and compare their attendance, fees and homework. |
| 7 | Accountant | Collect an outstanding fee, generate a receipt, inspect the report and approve/reject a fictional bank request. |
| 8 | Librarian | Issue an available copy, return it, then compare availability and member history. |
| 9 | Admin | Approve leave, enter marks and generate a normal or custom progress-card PDF. |

Optional operations include behaviour incidents, CBSE reports, ID cards/certificates, inventory issue/return, transport, hostel and payroll. Leave some records pending so the next role can demonstrate the corresponding action.

## Examinations and progress cards

For the normal seeded report, select the **Half Yearly Assessment 2026** group, **Half Yearly — September 2026**, session **2026–27**, Class **6**, Section **A**, and the **Interview Demo — Half Yearly Report** template. Match the exact spelling shown in your dropdowns.

The additional **Photo Style | Half Yearly + Annual Progress Card** template uses a dedicated progress-card assessment group and eight mapped component exams. Class 6-A has sample component marks. Configure mappings through **Photo Progress Card Settings** and enter/edit marks through the normal exam workflow. Annual component marks are explicitly simulated future examples.

The custom rules use 30/50/20 written components plus 10 internal marks per term. “Integer” is interpreted as internal assessment for this sample format. Missing marks must remain Pending.

## What this demo does not prove

Opening a payment/meeting/message page is not proof of a real transaction, API-created meeting or delivered message. Zoom, Meet, WhatsApp and gateway operations require the relevant external configuration. QR/thermal previews do not prove physical scanner/printer operation.

The recorded 43 checks cover role logins and selected pages. They are not a complete test of every submenu or every create/edit/delete combination.
