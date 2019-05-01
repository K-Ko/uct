# Users, allowed code sets and access levels

## Users

### Defined in code set "Users"

| Code        | Description      |
| ----------- | ---------------- |
| `user name` | `SHA1(password)` |

### CLI

Add an user or **overwrite** an existing user entry

    # ./uct user:add '<user>' '<password>'

Delete an user with **all** its access level definitions

    # ./uct user:delete '<user>'

List all users

    # ./uct user:list

## Allowed code sets and access levels

### Defined in code set "User access"

| Code                     | Description    |
| ------------------------ | -------------- |
| `<user name>`            | `<user level>` |
| `<user name>.<code set>` | `<user level>` |

Separate `<user name>` and `<code set>` if needed by `.` (dot) and
use the **technical** code set name here

A user can have rights to **all** code sets or only to **defined** code sets
with fine grained access levels.

### Access levels

-   `0` : **No access** at all; default if nothing was defined
-   `1` : **Display** translations read-only; e.g. for **quality checks**
-   `2` : **Translate** code descriptions, can't add or delete codes from code sets;
    e.g. for **translators**
-   `3` : **Translate** code descriptions, can also **add** and **delete** codes from code sets;
    e.g. for the **application developers**
-   `4` : **Maintain** application code sets itself, can also **translate**, **add** and
    **delete** code descriptions; e.g. for **application admin**
-   `9` : **Maintain** **ALL** (also system internal) code sets; **system admin** only!

> An user **without** any defined rights is handled like `<user name> = 0` and
> have therefore no access to any code set, can login but thats all.

> The minimum rights that an user should have is for example `<user name> = 1`
> which allows at least a look into the code sets.

> To maintain the **application code sets** itself, at least one **Application admin**
> with **level 4** is required!

### CLI

Use always the **technical** code set names.

Add an user right or overwrites an existing `<user>`.`<code set>` entry

    # ./uct acl:add '<user>' '<level>' ['<code set>']

Delete whole user access levels or only for one code set

    # ./uct acl:delete '<user>' ['<code set>']

List all user access definitions for one user

    # ./uct acl:list '<user>'

### Examples

| Definition   | Level | Comment                                                         | Role               |
| ------------ | ----- | --------------------------------------------------------------- | ------------------ |
|              |       | **user1**                                                       |                    |
| `user1`      | `1`   | Can **display** all **application code sets**                   | e.g. quality check |
|              |       | **user2**                                                       |                    |
| `user2`      | `2`   | Can **display** and **translate** all **application code sets** | as translator      |
|              |       | **user3**                                                       |                    |
| `user3.set1` | `2`   | Can **display** and **translate** only `set1`                   | as translator      |
|              |       | **user4**                                                       |                    |
| `user4.set1` | `2`   | Can **display** and **translate** `set1`                        | as translator      |
| `user4.set2` | `3`   | Can also **add** and **delete** codes in `set2`                 | as developer       |
|              |       | **user5**                                                       |                    |
| `user5`      | `4`   | Can fully **maintain** all application code sets                | application admin  |
|              |       | **system admin**                                                |                    |
| _`<admin>`_  | _`9`_ | _**Filled by app. init** and **MUST NOT** deleted!_             | system admin       |

#### **Code set specific** definitions have a **higher weighting** than `.*` definitions!

| Definition     | Level |
| -------------- | ----- |
| `userX`        | `2`   |
| `userX.set1`   | `1`   |
| `userX.secret` | `0`   |

> Can **display** and **translate** **all** application code sets,
> but can only **display** `set1` and have **NO** access to code set `secret`.

Can be read the other way around as

> Have **no access** to `secret`, can **display** `set1` only and can **translate** all other.
