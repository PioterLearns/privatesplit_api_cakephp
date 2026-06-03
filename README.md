Cost splitting service API with client-side encryption

Learning:
 - CakePHP - first time use
 - No AI code - as with all my learning type projects (so far) I do everything by hand

Roadmap:
- [x] 0.1 PoC - Deadline: 2026-05-20 (Wed)
 - encryption-less basic version of api logic (aka CakePHP basics)
- [x] 0.2 MVP - Deadline: 2026-05-27 (Wed)
 - Authentication and Authorization
 - Splitwise data import functionality
- [x] 0.2.1 Encryption [ADR-001](https://github.com/PioterLearns/privatesplit_api_cakephp/blob/roadmap-update/design/ADR/001.EncryptionScope.md)
- [ ] 0.3 Frontend readiness - Deadline: ~~2026-06-03 (Wed)~~ 2026-06-05 (Fri)
 - Clean up controllers, and settle on initial routes (start of BC effort)
 - Produce OpenAPI documentation
 - Commence work on frontend - heavily AI supported JS project. (I'm not very comfortable using AI for technology I don't know, but I am here to learn, so might as well give it a try)
- [ ] 0.4 Hardening pass - Deadline: 2026-06-30
 - DB transaction
 - Test coverage
 - Some loose ends, and read-up later TODOs
 - Security pass
- [ ] 1.0 MMP - Deadline: at some point. Maybe;)
 - Multi user buckets
 - Arbitrary precision amounts with strict balance control

Possible connected projects for future:
1. ~~Proper Web client (second attempt at learning proper frontend)~~ (Already planned)
2. Android client (learning Kotlin)
