UPGRADE FROM 6.x to 7.0
=======================

No binary compatibility break (BC break).

You should upgrade your version without troubles, 
except some deprecations messages (see [Client](#client) log).

Client
------

- Marked all ORM built-in methods as deprecated
  - **Use the record manager instead.**
- Fix [Issue 8](https://github.com/Aesislabs/php-odoo-api-client/issues/8)
  - Fixed method ```count()``` without criteria.
  - Added deprecated shortcut method ```countAll()```.

Endpoint
--------

- Added ```RemoteException``` with message and XML trace.
  - This exception extends ```RequestException```

DBAL
----

- Added record manager to manage models.
- Added schema to get model names and metadata.
- Added query builder and ORM query that allows to create queries easily in OOP context.
- Added record repository that allows to execute and isolate queries for a dedicated model.

Expression builder
------------------

- Implemented non-scalar values support:
    - Dates into string
    - Iterable / Generator into array
    - Object into string