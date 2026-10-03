# WORKFLOW MAP - Global Consultancy Education CRM

## Application Lifecycle State Machine

### Core Candidate Journey
```
Lead -> Candidate -> Counselling -> Profile Collection -> Academic Assessment -> 
English Assessment -> Course Shortlist -> Application -> Document Collection -> 
University Submission -> Offer (Conditional/Unconditional) -> Deposit -> CAS -> 
Visa -> Enrolment -> Pre-Departure -> Arrival -> Post-Arrival -> Completion -> 
Alumni -> Commission -> Invoice -> Payment
```

### Application Status Pipeline (Configurable)
```
DRAFT -> PROFILE_CHECK -> DOCUMENT_PENDING -> READY_TO_APPLY -> SUBMITTED -> 
ACKNOWLEDGED -> UNDER_REVIEW -> INTERVIEW_REQUIRED -> 
CONDITIONAL_OFFER -> UNCONDITIONAL_OFFER -> DEPOSIT_REQUIRED -> 
DEPOSIT_PAID -> CAS_REQUESTED -> CAS_ISSUED -> VISA_PREPARATION -> 
VISA_APPLIED -> VISA_APPROVED -> VISA_REFUSED -> ENROLLED -> 
WITHDRAWN -> REJECTED -> CLOSED
```

### Status History Protocol
**RULE:** Every status change MUST log a history entry. Never change status without history.

```
Status History Entry Structure:
- previous_status (varchar)
- new_status (varchar)
- changed_by (int - User FK)
- changed_at (timestamp - default now)
- reason (text - optional but recommended)
- note (text - optional)
```

### Workflow States Detail

#### 1. DRAFT
- Application created but not complete
- Candidate profile incomplete
- Documents not yet uploaded
- Can be edited freely
- No external actions possible

#### 2. PROFILE_CHECK
- Profile validation in progress
- Academic qualifications being verified
- English test results being confirmed
- Staff reviewing completeness

#### 3. DOCUMENT_PENDING
- Documents required but not all uploaded
- Document checklist being populated
- Missing documents identified
- Staff chasing for submissions

#### 4. READY_TO_APPLY
- All required documents uploaded
- Profile complete
- Candidate ready to submit application
- University selection confirmed
- Ready for submission

#### 5. SUBMITTED
- Application submitted to university
- University reference number received
- Submission date recorded
- No further edits possible
- Acknowledgment expected

#### 6. ACKNOWLEDGED
- University acknowledged receipt
- Application now in review queue
- Expected response timeframe known
- Candidate notified

#### 7. UNDER_REVIEW
- University evaluating application
- Admissions committee reviewing
- May request additional information
- Regular status update checks

#### 8. INTERVIEW_REQUIRED
- Interview scheduled or required
- Interview type (video, in-person, phone)
- Candidate notified with details
- Interview conducted or deferred

#### 9. CONDITIONAL_OFFER
- Conditional offer received from university
- Conditions specified (documents, grades, IELTS, etc.)
- Deadline to respond
- Candidate can accept conditions or request review

#### 10. UNCONDITIONAL_OFFER
- Unconditional offer received
- No further conditions
- Candidate must respond by deadline
- Deposit may be required

#### 11. DEPOSIT_REQUIRED
- Deposit needed to secure offer
- Amount specified
- Payment deadline
- Without deposit, offer expires

#### 12. DEPOSIT_PAID
- Deposit received
- Receipt recorded
- Transaction reference stored
- Next stage can proceed (CAS request)

#### 13. CAS_REQUESTED
- CAS (Confirmation of Acceptance for Studies) requested
- UKVI application started
- CAS number generated (when issued)
- CAS request fee paid (if applicable)

#### 14. CAS_ISSUED
- CAS number issued by university
- CAS reference number
- Validity period
- Used for visa application

#### 15. VISA_PREPARATION
- Visa documentation being prepared
- Financial evidence gathered
- Passport verified
- Biometrics appointment scheduled

#### 16. VISA_APPLIED
- Visa application submitted
- Application receipt number
- Biometrics completed (if applicable)
- Interview scheduled (if required)

#### 17. VISA_APPROVED
- Visa granted
- Entry visa received
- Travel can be arranged
- Student status confirmed

#### 18. VISA_REFUSED
- Visa application refused
- Reason(s) documented
- Appeal options (if available)
- May need to reapply or adjust plans

#### 19. ENROLLED
- Student enrolled at institution
- Student number assigned
- Orientation completed
- Semester start date

#### 20. WITHDRAWN
- Candidate withdrew application
- Written notification
- Reasons documented
- No further action

#### 21. REJECTED
- Application rejected by university
- Reason documented
- Feedback collected (if possible)
- Candidate notified

#### 22. CLOSED
- Journey complete (success or exit)
- No further actions possible
- Archive or delete as per retention policy