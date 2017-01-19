## Database
* db_create.sql - SQL for initial DB construction
* db_schema.png - visual DB schema

### To consider:
- [X] User access levels (per group, per user? static, defined by service, defined by organization?)
- [ ] Allow custom user role per Organization?
- [X] __lists__ per-user signup limit (ie: "2 spots per employee")
- [X] Multiple inheritance (better way to link multiple heirarchies - ie: user belongs to multiple organizations) [Refactored into reference tables]
- [ ] Recording of metadata (gender/age/..) for "plus one" guests (currently only recorded as __guests__.additional_guests:integer default(0))
- [X] Advanced pricing schema (pricing based on sex, time, etc.., ie: "Ladies free before 11, $10 after. Men $15 before 11, $20 after")
- [X] Concessions for "list administrator" ("Promoter" in current app terminology) - user that can view/admin other user's list submissions?