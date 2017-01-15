## Database
* db_create.sql - SQL for initial DB construction
* db_schema.png - visual DB schema

### To consider:
- [ ] User access levels (per group, per user? static, defined by service, defined by organization?)
- [ ] __lists__ per-user signup limit (ie: "2 spots per employee")
- [ ] Multiple inheritance (better way to link multiple heirarchies - ie: user belongs to multiple organizations, current: organization_id:varchar(255)::org_id_01,org_id_02)
- [ ] Recording of metadata (gender/age/..) for "plus one" guests (currently only recorded as __guests__.additional_guests:integer default(0))
- [ ] Advanced pricing schema (pricing based on sex, time, etc.., ie: "Ladies free before 11, $10 after. Men $15 before 11, $20 after")